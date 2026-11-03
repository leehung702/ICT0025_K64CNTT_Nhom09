<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài 06 - Tính tiền hàng và phí</title>
    <style>
        body { max-width: 800px; margin: 32px auto; padding: 0 20px; font-family: Arial, sans-serif; line-height: 1.7; color: #172554; }
        h1 { color: #1d4ed8; }
        ul { padding: 20px 20px 20px 40px; background: #eff6ff; border-radius: 8px; }
        .tong { font-size: 1.2em; font-weight: bold; }
    
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
        <a href="04_kieu_du_lieu.php">04 · Kiểu dữ liệu</a>
        <a href="05_toan_tu.php">05 · Toán tử</a>
        <a href="06_van_dung.php" aria-current="page">06 · Vận dụng</a>
    </nav>
</header>

<h1>Bài 06 - Tính tiền hàng và phí</h1>
<?php
$tenHang = 'Vở kẻ ngang';
$soLuong = 3;
$donGia = 25000;

// Tỷ lệ phí 5% được giữ cố định bằng một hằng.
const TY_LE_PHI = 0.05;
const DON_VI_TIEN = 'VND';

// Tính phí trên tiền hàng, không tính phí trên tổng đã có phí.
$tienHang = ($soLuong * $donGia);
$tienPhi = ($tienHang * TY_LE_PHI);
$tongTien = ($tienHang + $tienPhi);

// Hiển thị có nhãn; dấu chấm ngăn cách hàng nghìn, đơn vị tiền là VND.
echo '<ul>';
echo '<li>Mặt hàng: ' . $tenHang . '</li>';
echo '<li>Số lượng: ' . $soLuong . '</li>';
echo '<li>Đơn giá: ' . number_format($donGia, 0, ',', '.') . ' ' . DON_VI_TIEN . '</li>';
echo '<li>Tỷ lệ phí: ' . (TY_LE_PHI * 100) . '%</li>';
echo '<li>Tiền hàng: ' . number_format($tienHang, 0, ',', '.') . ' ' . DON_VI_TIEN . '</li>';
echo '<li>Tiền phí: ' . number_format($tienPhi, 0, ',', '.') . ' ' . DON_VI_TIEN . '</li>';
echo '<li class="tong">Tổng tiền: ' . number_format($tongTien, 0, ',', '.') . ' ' . DON_VI_TIEN . '</li>';
echo '</ul>';
?>
<p>Để thử bộ dữ liệu 2, sửa ba biến ở đầu khối PHP: tên hàng thành
    “Bút bi xanh”, số lượng thành 2 và đơn giá thành 18000; sau đó chạy lại.</p>
<p>Hai bộ dữ liệu mẫu cho số tiền nguyên VND; kết quả hiển thị làm tròn đến đơn vị VND.</p>
<details class="tip">
    <summary>Thử thách nhỏ</summary>
    <p>Thử đổi tên hàng, số lượng và đơn giá trong mã PHP rồi chạy lại; hằng phí vẫn giữ tỷ lệ 5%.</p>
</details>
</body>
</html>
