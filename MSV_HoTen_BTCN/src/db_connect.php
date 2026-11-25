<?php
// Kết nối CSDL dùng chung. Thông tin kết nối đọc từ config.php (ở thư mục gốc).
$configFile = __DIR__ . '/../config.php';
if (!file_exists($configFile)) {
    die('Chưa có config.php. Hãy sao chép config.example.php thành config.php.');
}
$cfg = require $configFile;

// Tắt chế độ ném ngoại lệ của PHP 8.1+ để kiểm tra lỗi bằng if như giáo trình.
mysqli_report(MYSQLI_REPORT_OFF);

$conn = new mysqli($cfg['host'], $cfg['user'], $cfg['pass'], $cfg['dbname']);
if ($conn->connect_error) {
    die('Kết nối CSDL thất bại. Hãy kiểm tra thông tin trong config.php.');
}
$conn->set_charset('utf8mb4');
