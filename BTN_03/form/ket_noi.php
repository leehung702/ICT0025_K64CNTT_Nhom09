<?php

$db_host = getenv("DB_HOST") ?: "127.0.0.1";
$db_name = getenv("DB_NAME") ?: "form_cccd_demo";
$db_user = getenv("DB_USER") ?: "root";
$db_password = getenv("DB_PASSWORD");

if ($db_password === false) {
    $db_password = "";
}

$dsn = "mysql:host=" . $db_host . ";dbname=" . $db_name . ";charset=utf8mb4";

$pdo = new PDO($dsn, $db_user, $db_password, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
