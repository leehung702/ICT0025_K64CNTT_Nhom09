<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Trang chủ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="khung">
        <h1>Xin chào!</h1>
        <p class="thong-bao-dung">Tạo hồ sơ cá nhân mẫu và đăng nhập để xem thông tin đã đăng ký.</p>
        <p class="thong-bao-dung">Mã hồ sơ được cấp tự động; đây không phải số CCCD.</p>

        <a class="nut" href="dang_nhap.php">Đăng nhập</a>
        <a class="nut nut-phu" href="dang_ky.php">Đăng ký</a>

        <p class="chuyen-trang">Hôm nay là ngày <?php echo date("d/m/Y"); ?></p>
    </div>
</body>
</html>
