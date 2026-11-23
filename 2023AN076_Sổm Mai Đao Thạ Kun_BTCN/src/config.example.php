<?php
declare(strict_types=1);

/*
 * Cấu hình kết nối CSDL (bản mẫu, KHÔNG chứa mật khẩu thật).
 * Cách dùng: sao chép file này thành config.php rồi sửa lại cho máy của bạn.
 * XAMPP mặc định: user = root, mật khẩu rỗng.
 */
$dbHost = '127.0.0.1';
$dbPort = '3306';
$dbName = 'quan_ly_diem_tv5';
$dbUser = 'root';
$dbPass = '';          // điền mật khẩu MySQL của máy bạn (nếu có) vào file config.php

$dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";

$pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,   // lỗi SQL -> ngoại lệ
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,                    // prepared statement thật
    PDO::MYSQL_ATTR_FOUND_ROWS   => true,                     // rowCount() = số dòng khớp WHERE
]);
