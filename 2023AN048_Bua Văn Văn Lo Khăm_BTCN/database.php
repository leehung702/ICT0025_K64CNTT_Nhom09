<?php
$configFile = __DIR__ . '/config.php';
$config = file_exists($configFile)
    ? require $configFile
    : require __DIR__ . '/config.example.php';

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

try {
    $conn = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Không thể kết nối cơ sở dữ liệu. Hãy kiểm tra MySQL và file config/config.php.');
}
