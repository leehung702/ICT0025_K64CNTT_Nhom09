<?php
// UPDATE: sửa điểm của một bản ghi xác định (theo id). Chỉ sửa điểm; cặp SV-HP giữ nguyên.
require 'db.php';
require 'helpers.php';

// Luôn đọc bản ghi từ CSDL theo id (không tin trạng thái do trình duyệt gửi lên).
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$stmt = $conn->prepare(
    "SELECT d.id, d.diem_qt, d.diem_thi, d.trang_thai, sv.ma_sv, sv.ho_ten, hp.ma_hp, hp.ten_hp
     FROM diem d
     INNER JOIN sinhvien sv ON d.sinhvien_id = sv.id
     INNER JOIN hocphan  hp ON d.hocphan_id  = hp.id
     WHERE d.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    http_response_code(404);
    ve_dau_trang('Không tìm thấy');
    echo '<p class="loi">Không tìm thấy bản ghi điểm.</p><p><a href="index.php">Quay lại</a></p>';
    ve_cuoi_trang();
    exit;
}
if ($row['trang_thai'] === 'Da_khoa') {
    http_response_code(403);
    ve_dau_trang('Điểm đã khóa');
    echo '<p class="loi">Điểm đã khóa, không thể sửa.</p><p><a href="index.php">Quay lại</a></p>';
    ve_cuoi_trang();
    exit;
}

$errors = [];
$raw_qt  = $row['diem_qt']  === null ? '' : (string)$row['diem_qt'];
$raw_thi = $row['diem_thi'] === null ? '' : (string)$row['diem_thi'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_qt  = (string)($_POST['diem_qt'] ?? '');
    $raw_thi = (string)($_POST['diem_thi'] ?? '');

    $loiDiem = [];                                   // lỗi riêng của phần điểm
    $qt  = doc_diem($raw_qt,  'Điểm quá trình', $loiDiem);
    $thi = doc_diem($raw_thi, 'Điểm thi',       $loiDiem);
    // Chỉ tính tổng kết khi từng ô điểm đã hợp lệ (tránh báo lỗi thừa)
    [$tk, $xl, $tt] = $loiDiem ? [null, null, 'Chua_nhap'] : tong_ket($qt, $thi, $loiDiem);
    $errors = array_merge($errors, $loiDiem);

    if (!$errors) {
        // WHERE id = ? : chỉ cập nhật ĐÚNG một bản ghi (và không đụng điểm đã khóa)
        $up = $conn->prepare(
            "UPDATE diem SET diem_qt = ?, diem_thi = ?, diem_tk = ?, xep_loai = ?, trang_thai = ?
             WHERE id = ? AND trang_thai <> 'Da_khoa'");
        $up->bind_param("dddssi", $qt, $thi, $tk, $xl, $tt, $id);
        $up->execute();
        $up->close();
        header("Location: index.php?msg=sua");
        exit;
    }
}

ve_dau_trang('Sửa điểm học phần');
?>
<p>Sinh viên: <b><?= h($row['ma_sv']) ?> - <?= h($row['ho_ten']) ?></b><br>
   Học phần: <b><?= h($row['ma_hp']) ?> - <?= h($row['ten_hp']) ?></b></p>

<?php if ($errors): ?>
<ul class="loi">
    <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
</ul>
<?php endif; ?>

<form method="POST" action="sua.php">
    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
    <label>Điểm quá trình (0–10, có thể để trống)</label>
    <input type="text" name="diem_qt" value="<?= h($raw_qt) ?>">
    <label>Điểm thi (0–10, có thể để trống)</label>
    <input type="text" name="diem_thi" value="<?= h($raw_thi) ?>">
    <p><button type="submit">Cập nhật</button> <a href="index.php">Quay lại danh sách</a></p>
</form>
<?php ve_cuoi_trang(); ?>
