<?php
$trangThai = 1;
$soLuong = 5;

// 1. Xử lý nhãn trạng thái (dùng switch từ Nhiệm vụ 2)
switch ($trangThai) {
    case 1:
        $nhanTrangThai = "Mới tạo";
        break;
    case 2:
        $nhanTrangThai = "Đang xử lý";
        break;
    default:
        $nhanTrangThai = "Không xác định";
        break;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý công việc - Phiên bản V0</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 30px auto;
            line-height: 1.6;
        }
        .khung {
            border: 1px solid #ccc;
            padding: 16px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <h2>Quản lý công việc (Phiên bản V0)</h2>

    <div class="khung">
        <p><strong>Biến trạng thái ($trangThai):</strong> <?php echo $trangThai; ?></p>
        <p><strong>Nhãn trạng thái:</strong> <?php echo $nhanTrangThai; ?></p>

        <hr>

        <p><strong>Danh sách công việc ($soLuong = <?php echo $soLuong; ?>):</strong></p>
        <?php
        // 2. Hiển thị danh sách đánh số (dùng for từ Nhiệm vụ 3)
        for ($i = 1; $i <= $soLuong; $i++) {
            echo "<p>Công việc " . $i . "</p>\n";
        }
        ?>
    </div>
</body>
</html>
