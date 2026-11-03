<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài 04 - Kiểu dữ liệu PHP</title>
    <style>
        body { max-width: 800px; margin: 32px auto; padding: 0 20px; font-family: Arial, sans-serif; line-height: 1.7; color: #172554; }
        h1, h2 { color: #1d4ed8; }
        pre { padding: 16px; background: #eff6ff; border-radius: 8px; white-space: pre-wrap; }
    
        .site-header { margin-bottom: 1.5rem; }
        .site-brand { margin: 0 0 .65rem; color: #64748b; font-size: .78rem; font-weight: 700; letter-spacing: .08em; text-transform: uppercase; }
        .lesson-nav { display: flex; flex-wrap: wrap; gap: .5rem; padding: .75rem; background: #eaf0ff; border-radius: 12px; }
        .lesson-nav a { color: #1e3a8a; text-decoration: none; font-size: .9rem; padding: .4rem .7rem; border-radius: 999px; background: #fff; transition: background-color .15s ease, transform .15s ease; }
        .lesson-nav a:hover { background: #dbeafe; transform: translateY(-1px); }
        .lesson-nav a:focus-visible { outline: 3px solid #93c5fd; outline-offset: 2px; }
        .lesson-nav a[aria-current="page"] { background: #1d4ed8; color: #fff; font-weight: 700; }
        .tip { margin: 1.5rem 0; padding: .9rem 1rem; border-left: 4px solid #2563eb; background: #eff6ff; border-radius: 0 10px 10px 0; }
        .tip summary { cursor: pointer; font-weight: 700; color: #1d4ed8; }
        .tip summary:focus-visible { outline: 2px solid #3b82f6; }
        .tip p { margin-bottom: 0; }
        @media (max-width: 600px) { .lesson-nav a { flex: 1 1 42%; text-align: center; } }
    </style>
</head>
<body>
<header class="site-header">
    <p class="site-brand">Bài thực hành PHP · 2.1–2.6</p>
    <nav class="lesson-nav" aria-label="Điều hướng sáu bài tập">
        <a href="01_cu_phap.php">01 · Cú pháp</a>
        <a href="02_bien.php">02 · Biến</a>
        <a href="03_hang.php">03 · Hằng</a>
        <a href="04_kieu_du_lieu.php" aria-current="page">04 · Kiểu dữ liệu</a>
        <a href="05_toan_tu.php">05 · Toán tử</a>
        <a href="06_van_dung.php">06 · Vận dụng</a>
    </nav>
</header>

<h1>Bài 04 - Kiểu dữ liệu PHP</h1>
<h2>Quan sát kiểu và giá trị bằng var_dump</h2>
<pre>
<?php
// Mỗi biến nhận giá trị thuộc một kiểu dữ liệu khác nhau.
$chuoi = 'PHP';
$soNguyen = 6;
$soThuc = 78.75;
$dangHoc = true;
$chuaCoDuLieu = null;

// var_dump hiển thị cả kiểu dữ liệu lẫn giá trị, không chỉ nội dung.
echo 'Chuỗi: ';
var_dump($chuoi);
echo 'Số nguyên: ';
var_dump($soNguyen);
echo 'Số thực: ';
var_dump($soThuc);
echo 'Boolean: ';
var_dump($dangHoc);
echo 'Null: ';
var_dump($chuaCoDuLieu);
?>
</pre>
<h2>Ép kiểu chuỗi số sang số nguyên</h2>
<pre>
<?php
$chuoiSo = "12";
$soNguyenEpKieu = (int) $chuoiSo;

// Ép kiểu tạo giá trị số nguyên mới; biến chuỗi số ban đầu không bị đổi kiểu.
echo 'Chuỗi số ban đầu: ';
var_dump($chuoiSo);
echo 'Sau khi ép bằng (int): ';
var_dump($soNguyenEpKieu);
?>
</pre>
<p>Nhận xét: cũng là 12 nhưng "12" là chuỗi, còn 12 sau khi ép kiểu là số nguyên.</p>
<details class="tip">
    <summary>Thử thách nhỏ</summary>
    <p>Mở và đóng phần này để xem lại nhận xét: chuỗi <code>"12"</code> và số nguyên <code>12</code> có kiểu khác nhau.</p>
</details>
</body>
</html>
