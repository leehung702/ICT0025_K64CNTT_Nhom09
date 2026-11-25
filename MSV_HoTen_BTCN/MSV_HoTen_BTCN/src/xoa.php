<?php
// DELETE: chỉ nhận form POST theo id. Điểm đã khóa (Da_khoa) không được xóa.
require 'db_connect.php';
require 'functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    chuyen_huong('index.php');
}

$id = filter_var(post_str('id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
if ($id === false) {
    chuyen_huong('index.php?msg=loi');
}

$stmt = $conn->prepare('SELECT trang_thai FROM diem WHERE id = ?');
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
    chuyen_huong('index.php?msg=xoa_khoa');
}

$stmt = $conn->prepare("DELETE FROM diem WHERE id = ? AND trang_thai <> 'Da_khoa'");
if (!$stmt) {
    chuyen_huong('index.php?msg=loi');
}
$stmt->bind_param('i', $id);
$stmt->execute();
$da_xoa = $stmt->affected_rows > 0;
$stmt->close();

chuyen_huong($da_xoa ? 'index.php?msg=xoa_ok' : 'index.php?msg=loi');
