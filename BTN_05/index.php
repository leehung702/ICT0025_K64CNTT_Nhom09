<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$errors = [];
$successMessages = [
    'added' => 'Đã lưu điểm học phần thành công.',
    'deleted' => 'Đã xóa điểm học phần thành công.',
];
$success = $successMessages[(string)($_GET['status'] ?? '')] ?? '';
$selectedStudent = '';
$studentNameInput = '';
$selectedCourse = '';
$scoreInput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $idToDelete = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
    if ($idToDelete === false || $idToDelete === null || $idToDelete < 1) {
        $errors[] = 'Mã điểm cần xóa không hợp lệ.';
    } else {
        $delete = $pdo->prepare('DELETE FROM diem WHERE id = :id');
        $delete->execute(['id' => $idToDelete]);
        if ($delete->rowCount() > 0) {
            header('Location: index.php?status=deleted');
            exit;
        }
        $errors[] = 'Không tìm thấy điểm cần xóa.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') !== 'delete') {
    $studentNameInput = trim((string)($_POST['ten_sinh_vien'] ?? ''));
    $selectedCourse = trim((string)($_POST['ma_hp'] ?? ''));
    $scoreInput = trim((string)($_POST['diem_so'] ?? ''));

    if ($studentNameInput === '') {
        $errors[] = 'Vui lòng nhập tên sinh viên.';
    }
    if ($selectedCourse === '') {
        $errors[] = 'Vui lòng chọn học phần.';
    }
    if ($scoreInput === '') {
        $errors[] = 'Vui lòng nhập điểm.';
    } elseif (!is_numeric($scoreInput) || (float)$scoreInput < 0 || (float)$scoreInput > 10) {
        $errors[] = 'Điểm phải là số trong khoảng từ 0 đến 10.';
    }

    if (!$errors) {
        $findStudent = $pdo->prepare('SELECT ma_sv FROM sinhvien WHERE ho_ten = :ho_ten ORDER BY ma_sv');
        $findStudent->execute(['ho_ten' => $studentNameInput]);
        $studentMatches = $findStudent->fetchAll();
        if (count($studentMatches) === 0) {
            $errors[] = 'Không tìm thấy sinh viên này. Hãy nhập/chọn tên có trong danh sách gợi ý.';
        } elseif (count($studentMatches) > 1) {
            $errors[] = 'Có nhiều sinh viên trùng tên; hãy dùng tên duy nhất trong danh sách.';
        } else {
            $selectedStudent = (string)$studentMatches[0]['ma_sv'];
        }

        $checkCourse = $pdo->prepare('SELECT COUNT(*) FROM hocphan WHERE ma_hp = :ma_hp');
        $checkCourse->execute(['ma_hp' => $selectedCourse]);
        if ((int)$checkCourse->fetchColumn() === 0) {
            $errors[] = 'Học phần được chọn không tồn tại.';
        }
    }

    if (!$errors) {
        try {
            $insert = $pdo->prepare(
                'INSERT INTO diem (ma_sv, ma_hp, diem_so)
                 VALUES (:ma_sv, :ma_hp, :diem_so)'
            );
            $insert->execute([
                'ma_sv' => $selectedStudent,
                'ma_hp' => $selectedCourse,
                'diem_so' => number_format((float)$scoreInput, 2, '.', ''),
            ]);
            header('Location: index.php?status=added');
            exit;
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'Sinh viên này đã có điểm cho học phần đã chọn.';
            } else {
                error_log($exception->getMessage());
                $errors[] = 'Không thể lưu dữ liệu. Hãy kiểm tra kết nối CSDL hoặc nhờ giảng viên hỗ trợ.';
            }
        }
    }
}

$students = $pdo->query(
    'SELECT sv.ma_sv, sv.ho_ten, l.ten_lop
     FROM sinhvien sv JOIN lop l ON l.ma_lop = sv.ma_lop
     ORDER BY sv.ho_ten'
)->fetchAll();
$courses = $pdo->query('SELECT ma_hp, ten_hp FROM hocphan ORDER BY ten_hp')->fetchAll();
$records = $pdo->query(
    'SELECT d.id, sv.ho_ten, l.ten_lop, hp.ten_hp, d.diem_so, d.ngay_tao
     FROM diem d
     JOIN sinhvien sv ON sv.ma_sv = d.ma_sv
     JOIN lop l ON l.ma_lop = sv.ma_lop
     JOIN hocphan hp ON hp.ma_hp = d.ma_hp
     ORDER BY d.id DESC'
)->fetchAll();

function classification(float $score): string
{
    if ($score >= 8.5) return 'Giỏi';
    if ($score >= 7.0) return 'Khá';
    if ($score >= 5.0) return 'Trung bình';
    return 'Yếu';
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sinh viên và điểm học phần</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <header class="hero">
        <p class="eyebrow">BTN 05 · INSERT, SELECT &amp; PREPARE</p>
        <h1>Quản lý sinh viên và điểm học phần</h1>
        <p>Nhập điểm học phần, kiểm tra dữ liệu và xem danh sách kết quả.</p>
    </header>

    <section class="panel" aria-labelledby="form-title">
        <h2 id="form-title">Thêm điểm học phần</h2>
        <?php if ($success !== ''): ?>
            <div class="message success" role="status"><?= e($success) ?></div>
        <?php endif; ?>
        <?php if ($errors): ?>
            <div class="message error" role="alert">
                <strong>Chưa thể lưu:</strong>
                <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <?php if (!$students || !$courses): ?>
            <div class="message error">Cần có ít nhất một sinh viên và một học phần trong CSDL trước khi nhập điểm.</div>
        <?php else: ?>
        <form method="post" action="index.php">
            <label for="ten_sinh_vien">Tên sinh viên <span>*</span></label>
            <input id="ten_sinh_vien" name="ten_sinh_vien" type="text" list="danh_sach_sinh_vien"
                   value="<?= e($studentNameInput) ?>" placeholder="Gõ tên sinh viên..." autocomplete="off" required>
            <datalist id="danh_sach_sinh_vien">
                <?php foreach ($students as $student): ?>
                    <option value="<?= e($student['ho_ten']) ?>" label="Lớp <?= e($student['ten_lop']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
            <p class="hint">Có thể gõ tên; chọn tên có sẵn trong gợi ý để khớp với dữ liệu sinh viên.</p>

            <label for="ma_hp">Học phần <span>*</span></label>
            <select id="ma_hp" name="ma_hp" required>
                <option value="">-- Chọn học phần --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= e($course['ma_hp']) ?>" <?= (string)$selectedCourse === (string)$course['ma_hp'] ? 'selected' : '' ?>>
                        <?= e($course['ten_hp']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="diem_so">Điểm (0–10) <span>*</span></label>
            <input id="diem_so" name="diem_so" type="number" min="0" max="10" step="0.01"
                   value="<?= e($scoreInput) ?>" placeholder="Ví dụ: 8.5" required>
            <button type="submit">Lưu điểm</button>
            <p class="hint">Tên sinh viên, học phần và điểm đều bắt buộc. Mỗi sinh viên chỉ có một kết quả cho mỗi học phần trong phiên bản bài tập này.</p>
        </form>
        <?php endif; ?>
    </section>

    <section class="panel" aria-labelledby="list-title">
        <div class="section-heading">
            <div><h2 id="list-title">Danh sách điểm</h2><p class="muted">Tổng số kết quả: <?= count($records) ?></p></div>
        </div>
        <?php if (!$records): ?>
            <div class="empty"><span class="empty-icon">＋</span><strong>Chưa có điểm nào</strong><span>Thêm kết quả bằng biểu mẫu phía trên để hiển thị tại đây.</span></div>
        <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead><tr><th>#</th><th>Sinh viên</th><th>Lớp</th><th>Học phần</th><th>Điểm</th><th>Xếp loại</th><th>Ngày tạo</th><th>Thao tác</th></tr></thead>
                <tbody>
                <?php foreach ($records as $record): $score = (float)$record['diem_so']; ?>
                    <tr>
                        <td><?= e($record['id']) ?></td>
                        <td><?= e($record['ho_ten']) ?></td>
                        <td><?= e($record['ten_lop']) ?></td>
                        <td><?= e($record['ten_hp']) ?></td>
                        <td><strong><?= e(number_format($score, 2)) ?></strong></td>
                        <td><span class="badge"><?= e(classification($score)) ?></span></td>
                        <td><?= e($record['ngay_tao']) ?></td>
                        <td>
                            <form method="post" action="index.php" class="delete-form"
                                  onsubmit="return confirm('Bạn chắc chắn muốn xóa điểm này?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= e($record['id']) ?>">
                                <button type="submit" class="delete-button">Xóa</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <footer>Nhóm 09 · Bài tập 05</footer>
</main>
</body>
</html>
