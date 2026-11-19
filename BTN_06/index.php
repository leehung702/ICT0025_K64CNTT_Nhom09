<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

$errors = [];
$messages = [
    'added'         => 'Đã lưu điểm học phần thành công.',
    'updated'       => 'Đã cập nhật điểm học phần thành công.',
    'deleted'       => 'Đã chuyển điểm vào thùng rác (xóa mềm). Có thể khôi phục ở tab "Thùng rác".',
    'restored'      => 'Đã khôi phục điểm học phần thành công.',
    'student_added' => 'Đã thêm sinh viên mới thành công.',
    'course_added'  => 'Đã thêm học phần mới thành công.',
];
$success = $messages[(string)($_GET['status'] ?? '')] ?? '';
$view = (($_GET['view'] ?? '') === 'trash') ? 'trash' : 'active';
$formAction = $view === 'trash' ? 'index.php?view=trash' : 'index.php';

// Giá trị giữ lại trong form khi có lỗi
$studentNameInput = '';
$selectedCourse = '';
$scoreInput = '';
$newStudentName = '';
$newStudentClass = '';
$newCourseCode = '';
$newCourseName = '';
$newCourseCredits = '3';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string)($_POST['action'] ?? '');

    if ($action === 'delete' || $action === 'restore') {
        // XÓA MỀM / KHÔI PHỤC: luôn có WHERE id = :id (khóa chính) và dùng tham số.
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id === false || $id === null || $id < 1) {
            $errors[] = 'Mã điểm không hợp lệ.';
        } elseif ($action === 'delete') {
            $stmt = $pdo->prepare('UPDATE diem SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id AND deleted_at IS NULL');
            $stmt->execute(['id' => $id]);
            if ($stmt->rowCount() === 1) {
                header('Location: index.php?status=deleted');
                exit;
            }
            $errors[] = 'Không tìm thấy điểm cần xóa (có thể đã bị xóa trước đó).';
        } else {
            $stmt = $pdo->prepare('UPDATE diem SET deleted_at = NULL WHERE id = :id AND deleted_at IS NOT NULL');
            $stmt->execute(['id' => $id]);
            if ($stmt->rowCount() === 1) {
                header('Location: index.php?view=trash&status=restored');
                exit;
            }
            $errors[] = 'Không tìm thấy điểm cần khôi phục.';
        }
    } elseif ($action === 'add_student') {
        $newStudentName = normalizeName((string)($_POST['new_student_name'] ?? ''));
        $newStudentClass = trim((string)($_POST['new_student_class'] ?? ''));

        if ($newStudentName === '') {
            $errors[] = 'Vui lòng nhập họ tên sinh viên mới.';
        } elseif (mb_strlen($newStudentName) > 120) {
            $errors[] = 'Họ tên sinh viên tối đa 120 ký tự.';
        }
        if ($newStudentClass === '') {
            $errors[] = 'Vui lòng chọn lớp cho sinh viên mới.';
        }
        if (!$errors) {
            $classCheck = $pdo->prepare('SELECT COUNT(*) FROM lop WHERE ma_lop = :ma_lop');
            $classCheck->execute(['ma_lop' => $newStudentClass]);
            if ((int)$classCheck->fetchColumn() === 0) {
                $errors[] = 'Lớp được chọn không tồn tại.';
            }
            $nameCheck = $pdo->prepare('SELECT COUNT(*) FROM sinhvien WHERE ho_ten = :ho_ten');
            $nameCheck->execute(['ho_ten' => $newStudentName]);
            if ((int)$nameCheck->fetchColumn() > 0) {
                $errors[] = 'Tên sinh viên này đã có trong danh sách. Để tránh nhầm lẫn, hãy nhập họ tên đầy đủ khác.';
            }
        }
        if (!$errors) {
            try {
                $addStudent = $pdo->prepare('INSERT INTO sinhvien (ho_ten, ma_lop) VALUES (:ho_ten, :ma_lop)');
                $addStudent->execute(['ho_ten' => $newStudentName, 'ma_lop' => $newStudentClass]);
                header('Location: index.php?status=student_added');
                exit;
            } catch (PDOException $exception) {
                error_log($exception->getMessage());
                $errors[] = 'Không thể thêm sinh viên. Hãy kiểm tra dữ liệu lớp hoặc cấu trúc CSDL.';
            }
        }
    } elseif ($action === 'add_course') {
        $newCourseCode = strtoupper(trim((string)($_POST['new_course_code'] ?? '')));
        $newCourseName = normalizeName((string)($_POST['new_course_name'] ?? ''));
        $newCourseCredits = trim((string)($_POST['new_course_credits'] ?? '3'));

        if ($newCourseCode === '') {
            $errors[] = 'Vui lòng nhập mã học phần.';
        } elseif (!preg_match('/^[A-Z0-9]{2,20}$/', $newCourseCode)) {
            $errors[] = 'Mã học phần chỉ gồm chữ không dấu và số, dài 2–20 ký tự (ví dụ NET201).';
        }
        if ($newCourseName === '') {
            $errors[] = 'Vui lòng nhập tên học phần.';
        } elseif (mb_strlen($newCourseName) > 120) {
            $errors[] = 'Tên học phần tối đa 120 ký tự.';
        }
        if (!ctype_digit($newCourseCredits) || (int)$newCourseCredits < 1 || (int)$newCourseCredits > 10) {
            $errors[] = 'Số tín chỉ phải là số nguyên từ 1 đến 10.';
        }
        if (!$errors) {
            $nameCheck = $pdo->prepare('SELECT COUNT(*) FROM hocphan WHERE ten_hp = :ten_hp');
            $nameCheck->execute(['ten_hp' => $newCourseName]);
            if ((int)$nameCheck->fetchColumn() > 0) {
                $errors[] = 'Tên học phần này đã có trong danh sách.';
            }
        }
        if (!$errors) {
            try {
                $addCourse = $pdo->prepare(
                    'INSERT INTO hocphan (ma_hp, ten_hp, so_tin_chi) VALUES (:ma_hp, :ten_hp, :so_tin_chi)'
                );
                $addCourse->execute([
                    'ma_hp' => $newCourseCode,
                    'ten_hp' => $newCourseName,
                    'so_tin_chi' => (int)$newCourseCredits,
                ]);
                header('Location: index.php?status=course_added');
                exit;
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $errors[] = 'Mã học phần này đã tồn tại; hãy nhập mã khác.';
                } else {
                    error_log($exception->getMessage());
                    $errors[] = 'Không thể thêm học phần. Hãy kiểm tra dữ liệu hoặc cấu trúc CSDL.';
                }
            }
        }
    } elseif ($action === 'add') {
        $studentNameInput = normalizeName((string)($_POST['ten_sinh_vien'] ?? ''));
        $selectedCourse = trim((string)($_POST['ma_hp'] ?? ''));
        $scoreInput = trim((string)($_POST['diem_so'] ?? ''));

        [$errors, $studentId] = validateGradeFields($pdo, $studentNameInput, $selectedCourse, $scoreInput);

        if (!$errors) {
            try {
                $scoreValue = number_format((float)$scoreInput, 2, '.', '');

                // UNIQUE(ma_sv, ma_hp) tính cả dòng đã xóa mềm: nếu cặp này đang ở thùng rác
                // thì dùng lại dòng đó thay vì báo lỗi khó hiểu.
                $find = $pdo->prepare('SELECT id, deleted_at FROM diem WHERE ma_sv = :ma_sv AND ma_hp = :ma_hp');
                $find->execute(['ma_sv' => $studentId, 'ma_hp' => $selectedCourse]);
                $existing = $find->fetch();

                if ($existing && $existing['deleted_at'] === null) {
                    $errors[] = 'Sinh viên này đã có điểm cho học phần đã chọn. Hãy dùng chức năng Sửa.';
                } elseif ($existing) {
                    $revive = $pdo->prepare(
                        'UPDATE diem
                         SET diem_so = :diem_so, deleted_at = NULL, ngay_tao = CURRENT_TIMESTAMP
                         WHERE id = :id AND deleted_at IS NOT NULL'
                    );
                    $revive->execute(['diem_so' => $scoreValue, 'id' => $existing['id']]);
                    header('Location: index.php?status=added');
                    exit;
                } else {
                    $insert = $pdo->prepare(
                        'INSERT INTO diem (ma_sv, ma_hp, diem_so) VALUES (:ma_sv, :ma_hp, :diem_so)'
                    );
                    $insert->execute(['ma_sv' => $studentId, 'ma_hp' => $selectedCourse, 'diem_so' => $scoreValue]);
                    header('Location: index.php?status=added');
                    exit;
                }
            } catch (PDOException $exception) {
                if ($exception->getCode() === '23000') {
                    $errors[] = 'Sinh viên này đã có điểm cho học phần đã chọn.';
                } else {
                    error_log($exception->getMessage());
                    $errors[] = 'Không thể lưu dữ liệu. Hãy kiểm tra kết nối CSDL hoặc nhờ giảng viên hỗ trợ.';
                }
            }
        }
    } else {
        $errors[] = 'Yêu cầu không hợp lệ.';
    }
}

$students = $pdo->query(
    'SELECT sv.ma_sv, sv.ho_ten, l.ten_lop
     FROM sinhvien sv JOIN lop l ON l.ma_lop = sv.ma_lop
     ORDER BY sv.ho_ten'
)->fetchAll();
$courses = $pdo->query('SELECT ma_hp, ten_hp FROM hocphan ORDER BY ten_hp')->fetchAll();
$classes = $pdo->query('SELECT ma_lop, ten_lop FROM lop ORDER BY ten_lop')->fetchAll();

$stats = $pdo->query(
    'SELECT COALESCE(SUM(deleted_at IS NULL), 0) AS active_count,
            COALESCE(SUM(deleted_at IS NOT NULL), 0) AS trash_count,
            AVG(CASE WHEN deleted_at IS NULL THEN diem_so END) AS avg_score
     FROM diem'
)->fetch();
$avgText = $stats['avg_score'] === null ? '—' : number_format((float)$stats['avg_score'], 2);

$condition = $view === 'trash' ? 'd.deleted_at IS NOT NULL' : 'd.deleted_at IS NULL';
$order = $view === 'trash' ? 'd.deleted_at DESC' : 'd.id DESC';
$records = $pdo->query(
    "SELECT d.id, sv.ho_ten, l.ten_lop, hp.ten_hp, d.diem_so, d.ngay_tao, d.deleted_at
     FROM diem d
     JOIN sinhvien sv ON sv.ma_sv = d.ma_sv
     JOIN lop l ON l.ma_lop = sv.ma_lop
     JOIN hocphan hp ON hp.ma_hp = d.ma_hp
     WHERE {$condition}
     ORDER BY {$order}"
)->fetchAll();
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Quản lý sinh viên và điểm học phần — BTN 06</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="container">
    <header class="hero">
        <p class="eyebrow">BTN 06 · UPDATE, DELETE &amp; WHERE</p>
        <h1>Quản lý sinh viên và điểm học phần</h1>
        <p>Thêm, xem, sửa và xóa mềm điểm học phần — có xác nhận trước khi xóa và có thể khôi phục.</p>
    </header>

    <section class="stats" aria-label="Thống kê nhanh">
        <div class="stat"><span class="stat-icon">📋</span><div><strong><?= (int)$stats['active_count'] ?></strong><span>Kết quả đang dùng</span></div></div>
        <div class="stat"><span class="stat-icon">⭐</span><div><strong><?= e($avgText) ?></strong><span>Điểm trung bình</span></div></div>
        <div class="stat"><span class="stat-icon">🎓</span><div><strong><?= count($students) ?></strong><span>Sinh viên</span></div></div>
        <div class="stat"><span class="stat-icon">📚</span><div><strong><?= count($courses) ?></strong><span>Học phần</span></div></div>
    </section>

    <?php if ($success !== ''): ?>
        <div class="alert alert-success" role="status"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="alert alert-error" role="alert">
            <strong>Chưa thể hoàn tất thao tác:</strong>
            <ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul>
        </div>
    <?php endif; ?>

    <div class="grid">
        <section class="card" aria-labelledby="add-title">
            <h2 id="add-title">Thêm điểm học phần</h2>
            <?php if (!$students || !$courses): ?>
                <div class="alert alert-error">Cần có ít nhất một sinh viên và một học phần trước khi nhập điểm.</div>
            <?php else: ?>
            <form method="post" action="<?= e($formAction) ?>" class="form">
                <input type="hidden" name="action" value="add">

                <label for="ten_sinh_vien">Tên sinh viên <span>*</span></label>
                <input id="ten_sinh_vien" name="ten_sinh_vien" type="text" list="ds_sinh_vien" maxlength="120"
                       value="<?= e($studentNameInput) ?>" placeholder="Gõ tên hoặc chọn từ gợi ý..." autocomplete="off" required>
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
                       value="<?= e($scoreInput) ?>" placeholder="Ví dụ: 8.5" required>

                <button type="submit" class="btn btn-primary">Lưu điểm</button>
                <p class="hint">Tên phải khớp sinh viên có sẵn. Mỗi sinh viên chỉ có một kết quả cho mỗi học phần.</p>
            </form>
            <?php endif; ?>
        </section>

        <section class="card" aria-labelledby="new-student-title">
            <h2 id="new-student-title">Thêm sinh viên mới</h2>
            <?php if (!$classes): ?>
                <p class="hint">Chưa có lớp trong CSDL; cần thêm lớp vào bảng <code>lop</code> trước.</p>
            <?php else: ?>
            <form method="post" action="<?= e($formAction) ?>" class="form">
                <input type="hidden" name="action" value="add_student">

                <label for="new_student_name">Họ tên sinh viên <span>*</span></label>
                <input id="new_student_name" name="new_student_name" type="text" maxlength="120"
                       value="<?= e($newStudentName) ?>" placeholder="Ví dụ: Phạm Minh Khoa" required>

                <label for="new_student_class">Lớp <span>*</span></label>
                <select id="new_student_class" name="new_student_class" required>
                    <option value="">-- Chọn lớp --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= e($class['ma_lop']) ?>" <?= $newStudentClass === (string)$class['ma_lop'] ? 'selected' : '' ?>>
                            <?= e($class['ten_lop']) ?> (<?= e($class['ma_lop']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn btn-secondary">Thêm sinh viên</button>
                <p class="hint">Sinh viên mới sẽ xuất hiện trong gợi ý khi nhập điểm.</p>
            </form>
            <?php endif; ?>
        </section>

        <section class="card" aria-labelledby="new-course-title">
            <h2 id="new-course-title">Thêm học phần mới</h2>
            <form method="post" action="<?= e($formAction) ?>" class="form">
                <input type="hidden" name="action" value="add_course">

                <label for="new_course_code">Mã học phần <span>*</span></label>
                <input id="new_course_code" name="new_course_code" type="text" maxlength="20"
                       value="<?= e($newCourseCode) ?>" placeholder="Ví dụ: NET201" required>

                <label for="new_course_name">Tên học phần <span>*</span></label>
                <input id="new_course_name" name="new_course_name" type="text" maxlength="120"
                       value="<?= e($newCourseName) ?>" placeholder="Ví dụ: Lập trình mạng" required>

                <label for="new_course_credits">Số tín chỉ</label>
                <input id="new_course_credits" name="new_course_credits" type="number" min="1" max="10" step="1"
                       value="<?= e($newCourseCredits) ?>" required>

                <button type="submit" class="btn btn-secondary">Thêm học phần</button>
                <p class="hint">Mã chỉ gồm chữ không dấu và số; mã không được trùng.</p>
            </form>
        </section>
    </div>

    <section class="card" aria-labelledby="list-title">
        <div class="card-head">
            <h2 id="list-title"><?= $view === 'trash' ? 'Thùng rác' : 'Danh sách điểm' ?></h2>
            <?php if ($records): ?>
                <input type="search" id="table-search" class="search" placeholder="Tìm sinh viên, lớp, học phần..." aria-label="Tìm trong bảng">
            <?php endif; ?>
        </div>

        <nav class="tabs" aria-label="Chế độ xem">
            <a class="tab <?= $view === 'active' ? 'active' : '' ?>" href="index.php">Điểm đang dùng <span class="count"><?= (int)$stats['active_count'] ?></span></a>
            <a class="tab <?= $view === 'trash' ? 'active' : '' ?>" href="index.php?view=trash">Thùng rác <span class="count"><?= (int)$stats['trash_count'] ?></span></a>
        </nav>

        <?php if (!$records): ?>
            <div class="empty">
                <span class="empty-icon"><?= $view === 'trash' ? '🗑' : '＋' ?></span>
                <?php if ($view === 'trash'): ?>
                    <strong>Thùng rác trống</strong><span>Các điểm đã xóa sẽ xuất hiện tại đây để có thể khôi phục.</span>
                <?php else: ?>
                    <strong>Chưa có điểm nào</strong><span>Thêm kết quả bằng biểu mẫu phía trên để hiển thị tại đây.</span>
                <?php endif; ?>
            </div>
        <?php else: ?>
        <div class="table-wrap">
            <table id="grade-table">
                <thead><tr>
                    <th>STT</th><th>Sinh viên</th><th>Lớp</th><th>Học phần</th><th>Điểm</th><th>Xếp loại</th>
                    <th><?= $view === 'trash' ? 'Thời điểm xóa' : 'Ngày tạo' ?></th><th class="col-actions">Thao tác</th>
                </tr></thead>
                <tbody>
                <?php foreach ($records as $index => $record): $score = (float)$record['diem_so']; ?>
                    <tr>
                        <td class="num"><?= $index + 1 ?></td>
                        <td><span class="person"><span class="avatar"><?= e(initials((string)$record['ho_ten'])) ?></span><?= e($record['ho_ten']) ?></span></td>
                        <td><?= e($record['ten_lop']) ?></td>
                        <td><?= e($record['ten_hp']) ?></td>
                        <td><strong class="score"><?= e(number_format($score, 2)) ?></strong></td>
                        <td><span class="badge <?= e(classificationClass($score)) ?>"><?= e(classification($score)) ?></span></td>
                        <td class="muted-cell"><?= e($view === 'trash' ? $record['deleted_at'] : $record['ngay_tao']) ?></td>
                        <td class="col-actions">
                            <div class="actions">
                            <?php if ($view === 'trash'): ?>
                                <form method="post" action="<?= e($formAction) ?>" class="inline-form">
                                    <input type="hidden" name="action" value="restore">
                                    <input type="hidden" name="id" value="<?= e($record['id']) ?>">
                                    <button type="submit" class="btn btn-success btn-sm">Khôi phục</button>
                                </form>
                            <?php else: ?>
                                <a class="btn btn-ghost btn-sm" href="edit.php?id=<?= e($record['id']) ?>">Sửa</a>
                                <form method="post" action="<?= e($formAction) ?>" class="inline-form"
                                      data-confirm="<?= e('Xóa điểm của ' . $record['ho_ten'] . ' – học phần ' . $record['ten_hp'] . '? Điểm sẽ được chuyển vào thùng rác và có thể khôi phục.') ?>"
                                      onsubmit="return confirm(this.dataset.confirm);">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?= e($record['id']) ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <footer>Nhóm 09 · Bài tập 06 · PHP + MySQL (PDO)</footer>
</main>

<script>
(function () {
    var search = document.getElementById('table-search');
    if (!search) { return; }
    search.addEventListener('input', function () {
        var q = search.value.trim().toLowerCase();
        document.querySelectorAll('#grade-table tbody tr').forEach(function (row) {
            row.hidden = q !== '' && row.textContent.toLowerCase().indexOf(q) === -1;
        });
    });
})();
</script>
</body>
</html>
