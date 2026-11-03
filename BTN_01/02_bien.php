<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài 02 - Biến trong PHP</title>
    <style>
        body { max-width: 800px; margin: 32px auto; padding: 0 20px; font-family: Arial, sans-serif; line-height: 1.7; color: #172554; }
        h1, h2 { color: #1d4ed8; }
    
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
        <a href="02_bien.php" aria-current="page">02 · Biến</a>
        <a href="03_hang.php">03 · Hằng</a>
        <a href="04_kieu_du_lieu.php">04 · Kiểu dữ liệu</a>
        <a href="05_toan_tu.php">05 · Toán tử</a>
        <a href="06_van_dung.php">06 · Vận dụng</a>
    </nav>
</header>

<?php
// Khai báo biến
$tenDoiTuong = 'Vở kẻ ngang';
$soLuong = 3;
$donGia = 25000;

echo '<h1>Bài 02 - Biến trong PHP</h1>';
echo '<h2>Giá trị ban đầu</h2>';
echo '<p>Tên đối tượng: ' . $tenDoiTuong . '</p>';
echo '<p>Số lượng: ' . $soLuong . '</p>';
echo '<p>Đơn giá: ' . number_format($donGia, 0, ',', '.') . ' VND</p>';

// Gán giá trị mới cho cùng một biến
$soLuong = 5;

echo '<h2>Sau khi đổi số lượng thành 5</h2>';
echo '<p>Tên đối tượng: ' . $tenDoiTuong . '</p>';
echo '<p>Số lượng: ' . $soLuong . '</p>';
echo '<p>Đơn giá: ' . number_format($donGia, 0, ',', '.') . ' VND</p>';

// Toán tử . dùng để nối chuỗi
echo '<p>Chuỗi sau khi nối: ' . $tenDoiTuong . ' | Số lượng: ' . $soLuong
    . ' | Đơn giá: ' . number_format($donGia, 0, ',', '.') . ' VND</p>';
?>
<details class="tip">
    <summary>Thử thách nhỏ</summary>
    <p>Thử gán một số lượng khác cho <code>$soLuong</code>, sau đó chạy lại trang để thấy biến có thể thay đổi.</p>
</details>
</body>
</html>
