<?php
$pageTitle = $pageTitle ?? 'Quản lý học phần';
require_once __DIR__ . '/functions.php';
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($pageTitle) ?> | Hệ thống quản lý học phần</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#f4f7fb}.navbar{box-shadow:0 2px 12px #152b4d12}
    .page-card{border:0;border-radius:16px;box-shadow:0 8px 28px #1831530d}
    .brand-mark{width:36px;height:36px;border-radius:10px;background:#e8f0ff;color:#2457c5;display:inline-grid;place-items:center;font-weight:800}
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white mb-4">
 <div class="container">
  <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="../index.php">
   <span class="brand-mark">HP</span> Quản lý học phần
  </a>
  <span class="navbar-text text-secondary small">Bài CRUD cá nhân – Thành viên 3</span>
 </div>
</nav>
<main class="container pb-5">
