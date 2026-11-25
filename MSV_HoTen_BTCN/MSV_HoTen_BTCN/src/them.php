<?php
// CREATE: nhập điểm mới.
require 'db_connect.php';
require 'functions.php';

$loi = [];
$values = ['sinhvien_id' => '', 'hocphan_id' => '', 'diem_qt' => '', 'diem_thi' => '', 'khoa' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Đọc dữ liệu POST
    foreach (['sinhvien_id', 'hocphan_id', 'diem_qt', 'diem_thi'] as $k) {
        $values[$k] = post_str($k);
    }
    $values['khoa'] = isset($_POST['khoa']) ? '1' : '';

    // 2. Kiểm tra dữ liệu (không lưu nếu có lỗi)
    $kq  = kiem_tra_diem($conn, $values);
    $loi = $kq['loi'];

    if (count($loi) === 0) {
        // 3. Tính điểm tổng kết, xếp loại, trạng thái
        $sv  = $kq['sv'];
        $hp  = $kq['hp'];
        $qt  = $kq['qt'];
        $thi = $kq['thi'];
        $tk  = tinh_diem_tk($qt, $thi);
        $xl  = xep_loai($tk);
        $tt  = ($values['khoa'] === '1') ? 'Da_khoa' : 'Da_nhap';

        // 4. INSERT bằng prepared statement
        $stmt = $conn->prepare(
            'INSERT INTO diem (sinhvien_id, hocphan_id, diem_qt, diem_thi, diem_tk, xep_loai, trang_thai)
             VALUES (?, ?, ?, ?, ?, ?, ?)'
        );
        if ($stmt) {
            $stmt->bind_param('iidddss', $sv, $hp, $qt, $thi, $tk, $xl, $tt);
            if ($stmt->execute()) {
                $stmt->close();
                chuyen_huong('index.php?msg=them_ok');
            }
            if ($stmt->errno === 1062) {
                $loi[] = 'Sinh viên này đã có điểm của học phần này. Hãy dùng chức năng Sửa.';
            } else {
                $loi[] = 'Không lưu được điểm, vui lòng thử lại.';
            }
            $stmt->close();
        } else {
            $loi[] = 'Không chuẩn bị được câu lệnh lưu, vui lòng thử lại.';
        }
    }
}

$ds_sv = lay_danh_sach($conn, 'SELECT id, ma_sv, ho_ten FROM sinhvien ORDER BY ma_sv');
$ds_hp = lay_danh_sach($conn, 'SELECT id, ma_hp, ten_hp FROM hocphan ORDER BY ma_hp');
$action = 'them.php';
$nut = 'Lưu điểm';

$tieu_de = 'Nhập điểm mới';
require 'header.php';
?>
<h1>Nhập điểm mới</h1>
<?php require 'form_diem.php'; ?>
<?php require 'footer.php'; ?>
