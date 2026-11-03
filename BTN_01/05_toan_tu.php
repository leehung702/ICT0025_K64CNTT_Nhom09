<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bài 05 - Toán tử PHP</title>
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
        <a href="04_kieu_du_lieu.php">04 · Kiểu dữ liệu</a>
        <a href="05_toan_tu.php" aria-current="page">05 · Toán tử</a>
        <a href="06_van_dung.php">06 · Vận dụng</a>
    </nav>
</header>

<h1>Bài 05 - Toán tử PHP</h1>
<h2>Số học và phép gán kết hợp</h2>
<pre>
<?php
// Hai số ban đầu đều khác 0, dùng ngoặc cho biểu thức
$soA = 12;
$soB = 5;

echo $soA . ' + ' . $soB . ' = ' . ($soA + $soB) . "\n";
echo $soA . ' - ' . $soB . ' = ' . ($soA - $soB) . "\n";
echo $soA . ' * ' . $soB . ' = ' . ($soA * $soB) . "\n";
echo $soA . ' / ' . $soB . ' = ' . ($soA / $soB) . "\n";
echo $soA . ' % ' . $soB . ' = ' . ($soA % $soB) . "\n";

// Phép gán kết hợp: $soA += 3 tương đương $soA = $soA + 3.
$soA += 3;
echo 'Sau phép $soA += 3: ' . $soA . "\n";
?>
</pre>
<h2>So sánh và biểu thức logic</h2>
<pre>
<?php
// var_dump giúp thấy rõ true/false thay vì chỉ thấy 1 hoặc chuỗi rỗng.
echo '5 == "5": ';
var_dump(5 == "5");
echo '5 === "5": ';
var_dump(5 === "5");

// && cho true khi cả hai điều kiện đều đúng (15 > 0 và 5 > 0).
echo '$soA > 0 && $soB > 0: ';
var_dump($soA > 0 && $soB > 0);
?>
</pre>
<p>== là so sánh bằng có quy tắc chuyển đổi kiểu; === yêu cầu cùng giá trị và cùng kiểu.</p>
<details class="tip">
    <summary>Thử thách nhỏ</summary>
    <p>Thử thay hai số bằng các số nguyên khác 0 rồi chạy lại để quan sát các phép tính và phép so sánh.</p>
</details>
</body>
</html>
