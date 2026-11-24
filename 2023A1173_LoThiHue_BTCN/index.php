<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/config.php';

/**
 * Hàm escape chuỗi khi hiển thị ra HTML để phòng chống XSS.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Lưu thông báo flash vào session rồi chuyển hướng (PRG pattern: Post/Redirect/Get).
 */
function redirectWithFlash(string $type, array $messages, string $query = ''): never
{
    $_SESSION['flash'] = [
        'type' => $type,
        'messages' => $messages,
    ];
    $location = 'index.php' . ($query !== '' ? '?' . $query : '');
    header('Location: ' . $location);
    exit;
}

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Lấy danh sách lớp học để hiển thị trong thẻ <select> và kiểm tra hợp lệ
$lopStmt = $pdo->query('SELECT ma_lop, ten_lop FROM lop ORDER BY ma_lop ASC');
$dsLop = $lopStmt->fetchAll();
$validMaLop = array_column($dsLop, 'ma_lop');

// Xử lý các yêu cầu POST (Thêm, Sửa, Chuyển vào thùng rác, Khôi phục, Xóa vĩnh viễn)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. THÊM SINH VIÊN MỚI
    if ($action === 'create') {
        $maSv = strtoupper(trim((string)($_POST['ma_sv'] ?? '')));
        $hoTen = trim((string)($_POST['ho_ten'] ?? ''));
        $maLop = trim((string)($_POST['ma_lop'] ?? ''));

        $errors = [];
        if ($maSv === '') {
            $errors[] = 'Mã sinh viên không được để trống.';
        } elseif (!preg_match('/^[A-Z0-9_-]{3,20}$/', $maSv)) {
            $errors[] = 'Mã sinh viên phải dài 3–20 ký tự, chỉ gồm chữ cái, số, gạch ngang hoặc gạch dưới.';
        }

        if ($hoTen === '') {
            $errors[] = 'Họ và tên sinh viên không được để trống.';
        } elseif (mb_strlen($hoTen) < 2 || mb_strlen($hoTen) > 120) {
            $errors[] = 'Họ và tên phải có độ dài từ 2 đến 120 ký tự.';
        }

        if ($maLop === '' || !in_array($maLop, $validMaLop, true)) {
            $errors[] = 'Vui lòng chọn lớp học hợp lệ trong danh sách.';
        }

        if (empty($errors)) {
            $checkStmt = $pdo->prepare('SELECT COUNT(*) FROM sinhvien WHERE ma_sv = :ma_sv');
            $checkStmt->execute([':ma_sv' => $maSv]);
            if ((int)$checkStmt->fetchColumn() > 0) {
                $errors[] = 'Mã sinh viên "' . $maSv . '" đã tồn tại trong hệ thống (kể cả trong thùng rác).';
            }
        }

        if (!empty($errors)) {
            $_SESSION['old_input'] = [
                'ma_sv' => $maSv,
                'ho_ten' => $hoTen,
                'ma_lop' => $maLop,
            ];
            redirectWithFlash('error', $errors, 'view=add');
        }

        $insertStmt = $pdo->prepare(
            'INSERT INTO sinhvien (ma_sv, ho_ten, ma_lop, is_deleted) VALUES (:ma_sv, :ho_ten, :ma_lop, 0)'
        );
        $insertStmt->execute([
            ':ma_sv' => $maSv,
            ':ho_ten' => $hoTen,
            ':ma_lop' => $maLop,
        ]);

        redirectWithFlash('success', ['Đã thêm sinh viên "' . $hoTen . '" (' . $maSv . ') thành công.']);
    }

    // 2. SỬA THÔNG TIN SINH VIÊN (theo khóa chính id)
    if ($action === 'update') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $maSv = strtoupper(trim((string)($_POST['ma_sv'] ?? '')));
        $hoTen = trim((string)($_POST['ho_ten'] ?? ''));
        $maLop = trim((string)($_POST['ma_lop'] ?? ''));

        if (!$id || $id <= 0) {
            redirectWithFlash('error', ['ID sinh viên cần sửa không hợp lệ.']);
        }

        $existStmt = $pdo->prepare('SELECT id FROM sinhvien WHERE id = :id AND is_deleted = 0');
        $existStmt->execute([':id' => $id]);
        if (!$existStmt->fetch()) {
            redirectWithFlash('error', ['Không tìm thấy sinh viên có ID = ' . $id . ' để cập nhật.']);
        }

        $errors = [];
        if ($maSv === '') {
            $errors[] = 'Mã sinh viên không được để trống.';
        } elseif (!preg_match('/^[A-Z0-9_-]{3,20}$/', $maSv)) {
            $errors[] = 'Mã sinh viên phải dài 3–20 ký tự, chỉ gồm chữ cái, số, gạch ngang hoặc gạch dưới.';
        }

        if ($hoTen === '') {
            $errors[] = 'Họ và tên sinh viên không được để trống.';
        } elseif (mb_strlen($hoTen) < 2 || mb_strlen($hoTen) > 120) {
            $errors[] = 'Họ và tên phải có độ dài từ 2 đến 120 ký tự.';
        }

        if ($maLop === '' || !in_array($maLop, $validMaLop, true)) {
            $errors[] = 'Vui lòng chọn lớp học hợp lệ trong danh sách.';
        }

        if (empty($errors)) {
            $dupStmt = $pdo->prepare('SELECT COUNT(*) FROM sinhvien WHERE ma_sv = :ma_sv AND id <> :id');
            $dupStmt->execute([
                ':ma_sv' => $maSv,
                ':id' => $id,
            ]);
            if ((int)$dupStmt->fetchColumn() > 0) {
                $errors[] = 'Mã sinh viên "' . $maSv . '" đã được gán cho sinh viên khác.';
            }
        }

        if (!empty($errors)) {
            redirectWithFlash('error', $errors, 'view=edit&id=' . $id);
        }

        $updateStmt = $pdo->prepare(
            'UPDATE sinhvien SET ma_sv = :ma_sv, ho_ten = :ho_ten, ma_lop = :ma_lop WHERE id = :id'
        );
        $updateStmt->execute([
            ':ma_sv' => $maSv,
            ':ho_ten' => $hoTen,
            ':ma_lop' => $maLop,
            ':id' => $id,
        ]);

        redirectWithFlash('success', ['Đã cập nhật thông tin sinh viên "' . $hoTen . '" thành công.']);
    }

    // 3A. XÓA TẠM (CHUYỂN VÀO THÙNG RÁC) THEO ID
    if ($action === 'trash') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id <= 0) {
            redirectWithFlash('error', ['ID sinh viên cần xóa không hợp lệ.']);
        }

        $trashStmt = $pdo->prepare('UPDATE sinhvien SET is_deleted = 1 WHERE id = :id AND is_deleted = 0');
        $trashStmt->execute([':id' => $id]);

        if ($trashStmt->rowCount() === 0) {
            redirectWithFlash('error', ['Không tìm thấy sinh viên có ID = ' . $id . ' để chuyển vào thùng rác.']);
        }

        redirectWithFlash('success', ['Đã chuyển sinh viên vào thùng rác.']);
    }

    // 3B. KHÔI PHỤC TỪ THÙNG RÁC THEO ID
    if ($action === 'restore') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id <= 0) {
            redirectWithFlash('error', ['ID sinh viên cần khôi phục không hợp lệ.'], 'view=trash');
        }

        $restoreStmt = $pdo->prepare('UPDATE sinhvien SET is_deleted = 0 WHERE id = :id AND is_deleted = 1');
        $restoreStmt->execute([':id' => $id]);

        if ($restoreStmt->rowCount() === 0) {
            redirectWithFlash('error', ['Không tìm thấy bản ghi trong thùng rác để khôi phục.'], 'view=trash');
        }

        redirectWithFlash('success', ['Đã khôi phục sinh viên về danh sách chính.'], 'view=trash');
    }

    // 3C. XÓA VĨNH VIỄN KHỎI CSDL (DELETE FROM sinhvien WHERE id = :id)
    if ($action === 'delete_permanent') {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id || $id <= 0) {
            redirectWithFlash('error', ['ID sinh viên cần xóa vĩnh viễn không hợp lệ.'], 'view=trash');
        }

        $deleteStmt = $pdo->prepare('DELETE FROM sinhvien WHERE id = :id');
        $deleteStmt->execute([':id' => $id]);

        if ($deleteStmt->rowCount() === 0) {
            redirectWithFlash('error', ['Không tìm thấy sinh viên có ID = ' . $id . ' hoặc bản ghi đã được xóa.'], 'view=trash');
        }

        redirectWithFlash('success', ['Đã xóa vĩnh viễn sinh viên khỏi cơ sở dữ liệu.'], 'view=trash');
    }

    redirectWithFlash('error', ['Hành động không hợp lệ.']);
}

// Xác định chế độ hiển thị: list (mặc định), add, edit, trash
$view = (string)($_GET['view'] ?? 'list');
if (!in_array($view, ['list', 'add', 'edit', 'trash'], true)) {
    $view = 'list';
}

// Nếu đang ở chế độ Sửa (?view=edit&id=...)
$editStudent = null;
if ($view === 'edit') {
    $editId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    if (!$editId || $editId <= 0) {
        $flash = ['type' => 'error', 'messages' => ['Tham số ID sinh viên cần sửa không hợp lệ.']];
        $view = 'list';
    } else {
        $findStmt = $pdo->prepare('SELECT id, ma_sv, ho_ten, ma_lop FROM sinhvien WHERE id = :id AND is_deleted = 0');
        $findStmt->execute([':id' => $editId]);
        $editStudent = $findStmt->fetch();
        if (!$editStudent) {
            $flash = ['type' => 'error', 'messages' => ['Không tìm thấy bản ghi sinh viên có ID = ' . $editId . '.']];
            $view = 'list';
        }
    }
}

$oldInput = $_SESSION['old_input'] ?? ['ma_sv' => '', 'ho_ten' => '', 'ma_lop' => ''];
unset($_SESSION['old_input']);

// Đếm số lượng trong thùng rác để hiển thị trên thanh điều hướng: Thùng rác (n)
$trashCount = (int)$pdo->query('SELECT COUNT(*) FROM sinhvien WHERE is_deleted = 1')->fetchColumn();

// Truy vấn danh sách sinh viên theo tab hiện tại (Danh sách hoạt động hoặc Thùng rác)
$isDeletedFlag = ($view === 'trash') ? 1 : 0;
$listStmt = $pdo->prepare(
    'SELECT s.id, s.ma_sv, s.ho_ten, s.ma_lop, l.ten_lop
     FROM sinhvien s
     INNER JOIN lop l ON s.ma_lop = l.ma_lop
     WHERE s.is_deleted = :is_deleted
     ORDER BY s.id ASC'
);
$listStmt->execute([':is_deleted' => $isDeletedFlag]);
$students = $listStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sinh viên — Lò Thị Huệ (2023A1173)</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="navbar">
    <div class="navbar-inner">
        <a href="index.php" class="brand">Quản lý sinh viên</a>
        <nav class="nav-links">
            <a href="index.php" class="nav-link <?= ($view === 'list' || $view === 'edit') ? 'active' : '' ?>">Danh sách</a>
            <a href="index.php?view=add" class="nav-link <?= $view === 'add' ? 'active' : '' ?>">Thêm sinh viên</a>
            <a href="index.php?view=trash" class="nav-link <?= $view === 'trash' ? 'active' : '' ?>">Thùng rác (<?= $trashCount ?>)</a>
        </nav>
    </div>
</header>

<main class="container">
    <div class="card">
        <?php if ($flash !== null): ?>
            <div class="alert <?= $flash['type'] === 'success' ? 'alert-success' : 'alert-error' ?>">
                <?php if (count($flash['messages']) === 1): ?>
                    <?= e($flash['messages'][0]) ?>
                <?php else: ?>
                    <strong>Vui lòng kiểm tra lại thông tin:</strong>
                    <ul>
                        <?php foreach ($flash['messages'] as $msg): ?>
                            <li><?= e($msg) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($view === 'add'): ?>
            <!-- MÀN HÌNH THÊM SINH VIÊN -->
            <div class="card-header">
                <h1 class="card-title">Thêm sinh viên mới</h1>
                <a href="index.php" class="btn btn-outline">← Quay lại danh sách</a>
            </div>

            <form method="post" action="index.php" class="crud-form" novalidate>
                <input type="hidden" name="action" value="create">

                <div class="form-group">
                    <label for="ma_sv">Mã sinh viên <span>*</span></label>
                    <input type="text" id="ma_sv" name="ma_sv" class="form-control" required maxlength="20"
                           value="<?= e($oldInput['ma_sv']) ?>" placeholder="Ví dụ: SV006">
                    <p class="form-hint">Dài 3–20 ký tự, gồm chữ cái, chữ số, gạch ngang hoặc gạch dưới (không trùng mã đã có).</p>
                </div>

                <div class="form-group">
                    <label for="ho_ten">Họ và tên <span>*</span></label>
                    <input type="text" id="ho_ten" name="ho_ten" class="form-control" required maxlength="120"
                           value="<?= e($oldInput['ho_ten']) ?>" placeholder="Ví dụ: Lò Thị Huệ">
                </div>

                <div class="form-group">
                    <label for="ma_lop">Lớp học <span>*</span></label>
                    <select id="ma_lop" name="ma_lop" class="form-control" required>
                        <option value="">-- Chọn lớp học --</option>
                        <?php foreach ($dsLop as $lop): ?>
                            <option value="<?= e($lop['ma_lop']) ?>" <?= $oldInput['ma_lop'] === $lop['ma_lop'] ? 'selected' : '' ?>>
                                <?= e($lop['ma_lop'] . ' - ' . $lop['ten_lop']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Lưu sinh viên</button>
                    <a href="index.php" class="btn btn-outline">Hủy</a>
                </div>
            </form>

        <?php elseif ($view === 'edit' && $editStudent !== null): ?>
            <!-- MÀN HÌNH SỬA SINH VIÊN -->
            <div class="card-header">
                <h1 class="card-title">Cập nhật thông tin sinh viên</h1>
                <a href="index.php" class="btn btn-outline">← Quay lại danh sách</a>
            </div>

            <form method="post" action="index.php" class="crud-form" novalidate>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" value="<?= (int)$editStudent['id'] ?>">

                <div class="form-group">
                    <label for="ma_sv">Mã sinh viên <span>*</span></label>
                    <input type="text" id="ma_sv" name="ma_sv" class="form-control" required maxlength="20"
                           value="<?= e($editStudent['ma_sv']) ?>">
                </div>

                <div class="form-group">
                    <label for="ho_ten">Họ và tên <span>*</span></label>
                    <input type="text" id="ho_ten" name="ho_ten" class="form-control" required maxlength="120"
                           value="<?= e($editStudent['ho_ten']) ?>">
                </div>

                <div class="form-group">
                    <label for="ma_lop">Lớp học <span>*</span></label>
                    <select id="ma_lop" name="ma_lop" class="form-control" required>
                        <option value="">-- Chọn lớp học --</option>
                        <?php foreach ($dsLop as $lop): ?>
                            <option value="<?= e($lop['ma_lop']) ?>" <?= $editStudent['ma_lop'] === $lop['ma_lop'] ? 'selected' : '' ?>>
                                <?= e($lop['ma_lop'] . ' - ' . $lop['ten_lop']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-footer">
                    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    <a href="index.php" class="btn btn-outline">Hủy sửa</a>
                </div>
            </form>

        <?php elseif ($view === 'trash'): ?>
            <!-- MÀN HÌNH THÙNG RÁC -->
            <div class="card-header">
                <h1 class="card-title">Thùng rác sinh viên</h1>
                <a href="index.php" class="btn btn-outline">← Quay lại danh sách</a>
            </div>

            <?php if (empty($students)): ?>
                <div class="empty-state">
                    <p>Thùng rác hiện đang trống.</p>
                    <a href="index.php" class="btn btn-primary">Về danh sách sinh viên</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã SV</th>
                                <th>Họ và tên</th>
                                <th>Lớp</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $sv): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= e($sv['ma_sv']) ?></td>
                                    <td><?= e($sv['ho_ten']) ?></td>
                                    <td><?= e($sv['ma_lop'] . ' - ' . $sv['ten_lop']) ?></td>
                                    <td>
                                        <div class="action-group">
                                            <form method="post" action="index.php" class="inline-form">
                                                <input type="hidden" name="action" value="restore">
                                                <input type="hidden" name="id" value="<?= (int)$sv['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Khôi phục</button>
                                            </form>
                                            <form method="post" action="index.php" class="inline-form"
                                                  onsubmit="return confirm('Xóa vĩnh viễn sinh viên <?= e($sv['ho_ten']) ?> (<?= e($sv['ma_sv']) ?>)? Hành động này không thể hoàn tác!');">
                                                <input type="hidden" name="action" value="delete_permanent">
                                                <input type="hidden" name="id" value="<?= (int)$sv['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Xóa vĩnh viễn</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- MÀN HÌNH DANH SÁCH SINH VIÊN CHÍNH -->
            <div class="card-header">
                <h1 class="card-title">Danh sách sinh viên</h1>
                <a href="index.php?view=add" class="btn btn-primary">+ Thêm sinh viên</a>
            </div>

            <?php if (empty($students)): ?>
                <div class="empty-state">
                    <p>Danh sách sinh viên hiện đang rỗng.</p>
                    <a href="index.php?view=add" class="btn btn-primary">+ Thêm sinh viên đầu tiên</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Mã SV</th>
                                <th>Họ và tên</th>
                                <th>Lớp</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $index => $sv): ?>
                                <tr>
                                    <td><?= $index + 1 ?></td>
                                    <td><?= e($sv['ma_sv']) ?></td>
                                    <td><?= e($sv['ho_ten']) ?></td>
                                    <td><?= e($sv['ma_lop'] . ' - ' . $sv['ten_lop']) ?></td>
                                    <td>
                                        <div class="action-group">
                                            <a href="index.php?view=edit&id=<?= (int)$sv['id'] ?>" class="btn btn-sm btn-outline">Sửa</a>
                                            <form method="post" action="index.php" class="inline-form"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn chuyển sinh viên <?= e($sv['ho_ten']) ?> (<?= e($sv['ma_sv']) ?>) vào thùng rác không?');">
                                                <input type="hidden" name="action" value="trash">
                                                <input type="hidden" name="id" value="<?= (int)$sv['id'] ?>">
                                                <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<footer>
    Bài tập cá nhân PHP &amp; MySQL — Lò Thị Huệ (MSSV: 2023A1173) — Thành viên 2, Nhóm 09
</footer>

</body>
</html>
