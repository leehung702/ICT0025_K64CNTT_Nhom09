<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$errors = [];
$record = null;

// 1. Lấy và kiểm tra ID (khóa chính) từ URL (GET) hoặc form (POST).
$rawId = $_SERVER['REQUEST_METHOD'] === 'POST' ? ($_POST['id'] ?? null) : ($_GET['id'] ?? null);
$id = filter_var($rawId, FILTER_VALIDATE_INT);

if ($id === false || $id === null || $id < 1) {
    http_response_code(400);
    $errors[] = 'Mã điểm không hợp lệ.';
} else {
    // 2. Tìm bản ghi theo khóa chính; bản ghi đã xóa mềm thì không cho sửa.
    $find = $pdo->prepare(
        'SELECT d.id, d.ma_sv, d.ma_hp, d.diem_so, sv.ho_ten
         FROM diem d
         JOIN sinhvien sv ON sv.ma_sv = d.ma_sv
         WHERE d.id = :id AND d.deleted_at IS NULL'
    );
    $find->execute(['id' => $id]);
    $record = $find->fetch() ?: null;

    if ($record === null) {
        http_response_code(404);
        $errors[] = 'Không tìm thấy điểm có mã ' . $id . ' (không tồn tại hoặc đã bị xóa).';
    }
}

// Giá trị hiển thị: dữ liệu hiện tại (GET) hoặc dữ liệu vừa gửi (POST bị lỗi).
$studentNameInput = $record ? (string)$record['ho_ten'] : '';
$selectedCourse = $record ? (string)$record['ma_hp'] : '';
$scoreInput = $record ? number_format((float)$record['diem_so'], 2, '.', '') : '';

if ($record !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $studentNameInput = normalizeName((string)($_POST['ten_sinh_vien'] ?? ''));
    $selectedCourse = trim((string)($_POST['ma_hp'] ?? ''));
    $scoreInput = trim((string)($_POST['diem_so'] ?? ''));

    [$errors, $studentId] = validateGradeFields($pdo, $studentNameInput, $selectedCourse, $scoreInput);

    if (!$errors) {
        $newScore = number_format((float)$scoreInput, 2, '.', '');
        $unchanged = $studentId === (string)$record['ma_sv']
            && $selectedCourse === (string)$record['ma_hp']
            && $newScore === number_format((float)$record['diem_so'], 2, '.', '');

        if ($unchanged) {
            $errors[] = 'Chưa có thay đổi nào so với dữ liệu hiện tại.';
        } else {
            // Không cho sửa thành cặp (sinh viên, học phần) đã thuộc dòng khác.
            $dup = $pdo->prepare(
                'SELECT COUNT(*) FROM diem WHERE ma_sv = :ma_sv AND ma_hp = :ma_hp AND id <> :id'
            );
            $dup->execute(['ma_sv' => $studentId, 'ma_hp' => $selectedCourse, 'id' => $id]);
            if ((int)$dup->fetchColumn() > 0) {
                $errors[] = 'Sinh viên này đã có điểm cho học phần đã chọn (có thể đang nằm trong thùng rác).';
            }
        }
    }

    if (!$errors) {
        try {
            // UPDATE luôn có WHERE theo khóa chính 
            $update = $pdo->prepare(
                'UPDATE diem
                 SET ma_sv = :ma_sv, ma_hp = :ma_hp, diem_so = :diem_so
                 WHERE id = :id AND deleted_at IS NULL'
            );
            $update->execute([
                'ma_sv' => $studentId,
                'ma_hp' => $selectedCourse,
                'diem_so' => $newScore,
                'id' => $id,
            ]);
            if ($update->rowCount() === 1) {
                header('Location: index.php?status=updated');
                exit;
            }
            $errors[] = 'Không cập nhật được (bản ghi có thể vừa bị xóa).';
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'Sinh viên này đã có điểm cho học phần đã chọn.';
            } else {
                error_log($exception->getMessage());
                $errors[] = 'Không thể cập nhật dữ liệu. Hãy kiểm tra kết nối CSDL hoặc nhờ giảng viên hỗ trợ.';
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
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sửa điểm học phần — BTN 06</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container container-narrow">
    <header class="hero hero-compact">
        <p class="eyebrow">BTN 06 · UPDATE &amp; WHERE</p>
        <h1><?= $record ? 'Sửa điểm #' . e($record['id']) : 'Không thể sửa điểm' ?></h1>
        <p>Chỉnh dữ liệu của một dòng điểm, xác định bằng khóa chính <code>id</code>.</p>
    </header>

    <?php if ($errors): ?>
        <div class="alert alert-error" role="alert">
            <strong>Chưa thể hoàn tất thao tác:</strong>
            <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <section class="card" aria-labelledby="edit-title">
        <h2 id="edit-title">Thông tin điểm</h2>

        <?php if ($record === null): ?>
            <div class="form-actions"><a class="btn btn-primary" href="index.php">← Quay lại danh sách</a></div>
        <?php else: ?>
        <form method="post" action="edit.php?id=<?= e($record['id']) ?>" class="form">
            <input type="hidden" name="id" value="<?= e($record['id']) ?>">

            <label for="ten_sinh_vien">Tên sinh viên <span>*</span></label>
            <input id="ten_sinh_vien" name="ten_sinh_vien" type="text" list="ds_sinh_vien" maxlength="120"
                   value="<?= e($studentNameInput) ?>" autocomplete="off" required>
            <datalist id="ds_sinh_vien">
                <?php foreach ($students as $student): ?>
                    <option value="<?= e($student['ho_ten']) ?>" label="Lớp <?= e($student['ten_lop']) ?>"></option>
                <?php endforeach; ?>
            </datalist>

            <label for="ma_hp">Học phần <span>*</span></label>
            <select id="ma_hp" name="ma_hp" required>
                <option value="">-- Chọn học phần --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= e($course['ma_hp']) ?>" <?= $selectedCourse === (string)$course['ma_hp'] ? 'selected' : '' ?>>
                        <?= e($course['ten_hp']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="diem_so">Điểm (0–10) <span>*</span></label>
            <input id="diem_so" name="diem_so" type="number" min="0" max="10" step="0.01"
                   value="<?= e($scoreInput) ?>" required>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                <a class="btn btn-ghost" href="index.php">Hủy sửa</a>
            </div>
            <p class="hint">Bản ghi chỉ được cập nhật nếu mã điểm còn tồn tại. Điểm phải từ 0 đến 10.</p>
        </form>
        <?php endif; ?>
    </section>

    <footer>Nhóm 09 · Bài tập 06 · PHP + MySQL (PDO)</footer>
</main>
</body>
</html>
