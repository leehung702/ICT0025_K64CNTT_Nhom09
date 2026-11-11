<?php
session_start();
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $tham_so_cookie = session_get_cookie_params();
    setcookie(
        session_name(),
        "",
        time() - 42000,
        $tham_so_cookie["path"],
        $tham_so_cookie["domain"],
        $tham_so_cookie["secure"],
        $tham_so_cookie["httponly"]
    );
}

session_destroy();
header("Location: dang_nhap.php");
exit;
