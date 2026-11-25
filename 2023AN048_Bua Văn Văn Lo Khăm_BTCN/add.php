<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
$pageTitle = 'Thêm học phần';
$errors = [];
$ma_hp = $ten_hp = '';
$so_tin_chi = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $ma_hp = strtoupper(trim($_POST['ma_hp'] ?? ''));
    $ten_hp = trim($_POST['ten_hp'] ?? '');
    $so_tin_chi = trim($_POST['so_tin_chi'] ?? '');
    if ($ma_hp === '' || mb_strlen($ma_hp) > 20) $errors[] = 'Mã học phần không được để trống và tối đa 20 ký tự.';
    if ($ten_hp === '' || mb_strlen($ten_hp) > 150) $errors[] = 'Tên học phần không được để trống và tối đa 150 ký tự.';
    if (filter_var($so_tin_chi, FILTER_VALIDATE_INT) === false || (int)$so_tin_chi < 1 || (int)$so_tin_chi > 30) $errors[] = 'Số tín chỉ phải là số nguyên từ 1 đến 30.';
    if (!$errors) {
        $check = $conn->prepare('SELECT id FROM hocphan WHERE ma_hp = ?');
        $check->execute([$ma_hp]);
        if ($check->fetch()) $errors[] = 'Mã học phần đã tồn tại.';
    }
    if (!$errors) {
        $stmt = $conn->prepare('INSERT INTO hocphan (ma_hp, ten_hp, so_tin_chi) VALUES (?, ?, ?)');
        $stmt->execute([$ma_hp, $ten_hp, (int)$so_tin_chi]);
        $_SESSION['flash'] = 'Thêm học phần thành công.';
        header('Location: index.php'); exit;
    }
}
require __DIR__ . '/../includes/header.php';
?>
<div class="mb-4"><a href="index.php" class="text-decoration-none">← Danh sách học phần</a><h1 class="h3 fw-bold mt-2">Thêm học phần</h1></div>
<div class="card page-card"><div class="card-body p-4">
 <?php if ($errors): ?><div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $err): ?><li><?= e($err) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
 <form method="post" novalidate>
  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
  <div class="mb-3"><label class="form-label fw-semibold">Mã học phần</label><input class="form-control" name="ma_hp" maxlength="20" required value="<?= e($ma_hp) ?>" placeholder="Ví dụ: HP004"></div>
  <div class="mb-3"><label class="form-label fw-semibold">Tên học phần</label><input class="form-control" name="ten_hp" maxlength="150" required value="<?= e($ten_hp) ?>" placeholder="Nhập tên học phần"></div>
  <div class="mb-4"><label class="form-label fw-semibold">Số tín chỉ</label><input class="form-control" type="number" name="so_tin_chi" min="1" max="30" required value="<?= e($so_tin_chi) ?>"></div>
  <button class="btn btn-primary px-4">Lưu học phần</button> <a class="btn btn-light" href="index.php">Hủy</a>
 </form>
</div></div>
<?php require __DIR__ . '/../includes/footer.php'; ?>
