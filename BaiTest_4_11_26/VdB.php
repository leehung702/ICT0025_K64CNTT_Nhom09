<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ví dụ B</title>
</head>
<body>
    <h2>Ví dụ B — switch</h2>
    <?php
    $trangThai = 2;
    switch ($trangThai) {
        case 1:
            echo "Mới tạo";
            break;
        case 2:
            echo "Đang xử lý";
            break;
        default:
            echo "Không xác định";
    }
    ?>
</body>
</html>