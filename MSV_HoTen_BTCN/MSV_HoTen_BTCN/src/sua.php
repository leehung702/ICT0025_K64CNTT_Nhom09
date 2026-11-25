<?php
// UPDATE: sửa điểm. Điểm đã khóa (Da_khoa) không được sửa.
require 'db_connect.php';
require 'functions.php';

$id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    chuyen_huong('index.php?msg=loi');
}

// Lấy bản ghi hiện tại
$stmt = $conn->prepare('SELECT sinhvien_id, hocphan_id, diem_qt, diem_thi, trang_thai FROM diem WHERE id = ?');
if (!$stmt) {
    chuyen_huong('index.php?msg=loi');
}
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res ? $res->fetch_assoc() : null;
$stmt->close();

if (!$row) {
    chuyen_huong('index.php?msg=khong_thay');
}
if ($row['trang_thai'] === 'Da_khoa') {
    chuyen_huong('index.php?msg=sua_khoa');
}

$loi = [];
$values = [
    'sinhvien_id' => (string)$row['sinhvien_id'],
    'hocphan_id'  => (string)$row['hocphan_id'],
    'diem_qt'     => $row['diem_qt']  === null ? '' : (string)$row['diem_qt'],
    'diem_thi'    => $row['diem_thi'] === null ? '' : (string)$row['diem_thi'],
    'khoa'        => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach (['sinhvien_id', 'hocphan_id', 'diem_qt', 'diem_thi'] as $k) {
        $values[$k] = post_str($k);
    }
    $values['khoa'] = isset($_POST['khoa']) ? '1' : '';

    $kq  = kiem_tra_diem($conn, $values);
    $loi = $kq['loi'];

    if (count($loi) === 0) {
        $sv  = $kq['sv'];
        $hp  = $kq['hp'];
        $qt  = $kq['qt'];
        $thi = $kq['thi'];
        $tk  = tinh_diem_tk($qt, $thi);
        $xl  = xep_loai($tk);
        $tt  = ($values['khoa'] === '1') ? 'Da_khoa' : 'Da_nhap';

        $stmt = $conn->prepare(
            "UPDATE diem
             SET sinhvien_id = ?, hocphan_id = ?, diem_qt = ?, diem_thi = ?,
                 diem_tk = ?, xep_loai = ?, trang_thai = ?
             WHERE id = ? AND trang_thai <> 'Da_khoa'"
        );
        if ($stmt) {
            $stmt->bind_param('iidddssi', $sv, $hp, $qt, $thi, $tk, $xl, $tt, $id);
            if ($stmt->execute()) {
                $stmt->close();
                chuyen_huong('index.php?msg=sua_ok');
            }
            if ($stmt->errno === 1062) {
                $loi[] = 'Sinh viên này đã có điểm của học phần này ở một dòng khác.';
            } else {
                $loi[] = 'Không cập nhật được điểm, vui lòng thử lại.';
            }
            $stmt->close();
        } else {
            $loi[] = 'Không chuẩn bị được câu lệnh cập nhật, vui lòng thử lại.';
        }
    }
}

$ds_sv = lay_danh_sach($conn, 'SELECT id, ma_sv, ho_ten FROM sinhvien ORDER BY ma_sv');
$ds_hp = lay_danh_sach($conn, 'SELECT id, ma_hp, ten_hp FROM hocphan ORDER BY ma_hp');
$action = 'sua.php?id=' . $id;
$nut = 'Cập nhật';

$tieu_de = 'Sửa điểm';
require 'header.php';
?>
<h1>Sửa điểm</h1>
<?php require 'form_diem.php'; ?>
<?php require 'footer.php'; ?>
