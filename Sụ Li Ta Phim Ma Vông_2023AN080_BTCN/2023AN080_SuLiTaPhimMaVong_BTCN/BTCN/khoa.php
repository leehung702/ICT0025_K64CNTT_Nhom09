<?php
// Khóa điểm: Da_nhap -> Da_khoa (sau khi khóa không sửa/xóa được). Chỉ nhận POST.
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die("Chỉ chấp nhận POST.");
}
$id = (int)($_POST['id'] ?? 0);

$stmt = $conn->prepare("UPDATE diem SET trang_thai = 'Da_khoa' WHERE id = ? AND trang_thai = 'Da_nhap'");
$stmt->bind_param("i", $id);
$stmt->execute();
$ok = $stmt->affected_rows > 0;
$stmt->close();

header("Location: index.php?msg=" . ($ok ? "khoa" : "khongkhoa"));
exit;
