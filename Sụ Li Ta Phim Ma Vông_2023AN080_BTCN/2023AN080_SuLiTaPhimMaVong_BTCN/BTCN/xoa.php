<?php
// DELETE: chỉ nhận POST (không xóa qua đường link GET), xóa theo id.
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Chỉ chấp nhận POST.");
}
$id = (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare("DELETE FROM diem WHERE id = ? AND trang_thai <> 'Da_khoa'");
$stmt->bind_param("i", $id);
$stmt->execute();
$daXoa = $stmt->affected_rows > 0;
$stmt->close();

header("Location: index.php?msg=" . ($daXoa ? "xoa" : "khongxoa"));
exit;
