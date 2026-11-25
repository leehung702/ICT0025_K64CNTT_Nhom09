<?php
// CREATE: thêm điểm cho một (sinh viên, học phần).
require 'db.php';
require 'helpers.php';

$errors = [];
$sv_id = 0; $hp_id = 0; $raw_qt = ''; $raw_thi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1) Đọc dữ liệu form
    $sv_id   = (int)($_POST['sinhvien_id'] ?? 0);
    $hp_id   = (int)($_POST['hocphan_id'] ?? 0);
    $raw_qt  = (string)($_POST['diem_qt'] ?? '');
    $raw_thi = (string)($_POST['diem_thi'] ?? '');

    // 2) Kiểm tra dữ liệu (nhánh lỗi) - chỉ lưu khi $errors rỗng
    if ($sv_id <= 0) $errors[] = "Hãy chọn sinh viên.";
    if ($hp_id <= 0) $errors[] = "Hãy chọn học phần.";
    $loiDiem = [];                                   // lỗi riêng của phần điểm
    $qt  = doc_diem($raw_qt,  'Điểm quá trình', $loiDiem);
    $thi = doc_diem($raw_thi, 'Điểm thi',       $loiDiem);
    // Chỉ tính tổng kết khi từng ô điểm đã hợp lệ (tránh báo lỗi thừa)
    [$tk, $xl, $tt] = $loiDiem ? [null, null, 'Chua_nhap'] : tong_ket($qt, $thi, $loiDiem);
    $errors = array_merge($errors, $loiDiem);

    // 3) Hợp lệ -> INSERT bằng prepared statement
    if (!$errors) {
        $stmt = $conn->prepare(
            "INSERT INTO diem (sinhvien_id, hocphan_id, diem_qt, diem_thi, diem_tk, xep_loai, trang_thai)
             VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidddss", $sv_id, $hp_id, $qt, $thi, $tk, $xl, $tt);
        try {
            $stmt->execute();
            $stmt->close();
            header("Location: index.php?msg=them");
            exit;
        } catch (mysqli_sql_exception $e) {
            if ($e->getCode() === 1062) {
                $errors[] = "Sinh viên này đã có điểm học phần này. Hãy dùng chức năng Sửa.";
            } elseif ($e->getCode() === 1452) {
                $errors[] = "Sinh viên hoặc học phần không tồn tại.";
            } else {
                $errors[] = "Lỗi lưu dữ liệu, vui lòng thử lại.";
            }
        }
    }
}

// Dữ liệu cho 2 ô chọn
$ds_sv = $conn->query("SELECT id, ma_sv, ho_ten FROM sinhvien ORDER BY ma_sv")->fetch_all(MYSQLI_ASSOC);
$ds_hp = $conn->query("SELECT id, ma_hp, ten_hp FROM hocphan ORDER BY ma_hp")->fetch_all(MYSQLI_ASSOC);

ve_dau_trang('Thêm điểm học phần');
?>
<?php if ($errors): ?>
<ul class="loi">
    <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="POST" action="them.php">
    <label>Sinh viên (*)</label>
    <select name="sinhvien_id">
        <option value="0">-- Chọn sinh viên --</option>
        <?php foreach ($ds_sv as $sv): ?>
        <option value="<?= (int)$sv['id'] ?>" <?= $sv_id === (int)$sv['id'] ? 'selected' : '' ?>>
            <?= h($sv['ma_sv']) ?> - <?= h($sv['ho_ten']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Học phần (*)</label>
    <select name="hocphan_id">
        <option value="0">-- Chọn học phần --</option>
        <?php foreach ($ds_hp as $hp): ?>
        <option value="<?= (int)$hp['id'] ?>" <?= $hp_id === (int)$hp['id'] ? 'selected' : '' ?>>
            <?= h($hp['ma_hp']) ?> - <?= h($hp['ten_hp']) ?></option>
        <?php endforeach; ?>
    </select>

    <label>Điểm quá trình (0–10, có thể để trống)</label>
    <input type="text" name="diem_qt" value="<?= h($raw_qt) ?>">

    <label>Điểm thi (0–10, có thể để trống)</label>
    <input type="text" name="diem_thi" value="<?= h($raw_thi) ?>">

    <p><button type="submit">Lưu</button> <a href="index.php">Quay lại danh sách</a></p>
</form>
<?php ve_cuoi_trang(); ?>
