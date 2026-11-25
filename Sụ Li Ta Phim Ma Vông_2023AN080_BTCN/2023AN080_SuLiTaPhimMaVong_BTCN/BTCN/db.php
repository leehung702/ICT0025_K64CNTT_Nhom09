<?php
// Kết nối MySQL dùng chung cho mọi trang. Mỗi trang chỉ cần: require 'db.php';
$cfgFile = __DIR__ . '/config.php';
if (!file_exists($cfgFile)) {
    die("Chưa có config.php. Hãy sao chép config.example.php thành config.php và điền thông tin.");
}
$cfg = require $cfgFile;

// Báo lỗi SQL bằng ngoại lệ (mysqli_sql_exception) để dùng được try/catch.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['name']);
    $conn->set_charset('utf8mb4');          // đọc/ghi tiếng Việt đúng
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    die("Không kết nối được CSDL. Kiểm tra MySQL đã chạy, đã import database.sql và config.php đúng chưa.");
}
