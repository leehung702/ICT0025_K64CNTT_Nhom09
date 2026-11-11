<?php
session_start();

$email = "";
$mat_khau = "";
$loi = [];
$ho_so = null;
$loi_co_so_du_lieu = "";
$hien_thong_bao_quen_mat_khau = isset($_GET["quen_mat_khau"]);
$thoi_gian_dang_nhap = isset($_SESSION["thoi_gian_dang_nhap"])
    ? $_SESSION["thoi_gian_dang_nhap"] : "";

try {
    if (isset($_SESSION["nguoi_dung_id"])) {
        require __DIR__ . "/ket_noi.php";
        $stmt = $pdo->prepare(
            "SELECT ma_ho_so, ho_ten, ngay_sinh, gioi_tinh, quoc_tich, que_quan,
                    dia_chi_thuong_tru, dia_chi_hien_tai, so_dien_thoai, email
             FROM nguoi_dung WHERE id = :id"
        );
        $stmt->execute([":id" => $_SESSION["nguoi_dung_id"]]);
        $ho_so = $stmt->fetch();

        if (!$ho_so) {
            $_SESSION = [];
            session_destroy();
            $thoi_gian_dang_nhap = "";
        }
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && !$ho_so) {
        $email = isset($_POST["email"]) && is_string($_POST["email"]) ? trim($_POST["email"]) : "";
        $mat_khau = isset($_POST["mat_khau"]) && is_string($_POST["mat_khau"]) ? $_POST["mat_khau"] : "";

        if ($email == "") {
            $loi["email"] = "Vui lòng nhập email.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $loi["email"] = "Email không đúng định dạng.";
        }
        if ($mat_khau == "") {
            $loi["mat_khau"] = "Vui lòng nhập mật khẩu.";
        }

        if (count($loi) == 0) {
            require __DIR__ . "/ket_noi.php";
            $stmt = $pdo->prepare(
                "SELECT id, ma_ho_so, ho_ten, ngay_sinh, gioi_tinh, quoc_tich, que_quan,
                        dia_chi_thuong_tru, dia_chi_hien_tai, so_dien_thoai, email, mat_khau
                 FROM nguoi_dung WHERE email = :email"
            );
            $stmt->execute([":email" => $email]);
            $nguoi_dung = $stmt->fetch();

            if (!$nguoi_dung || !password_verify($mat_khau, $nguoi_dung["mat_khau"])) {
                $loi["dang_nhap"] = "Email hoặc mật khẩu không đúng.";
            } else {
                session_regenerate_id(true);
                $_SESSION["nguoi_dung_id"] = $nguoi_dung["id"];
                $_SESSION["thoi_gian_dang_nhap"] = date("d/m/Y H:i:s");
                $thoi_gian_dang_nhap = $_SESSION["thoi_gian_dang_nhap"];
                unset($nguoi_dung["id"], $nguoi_dung["mat_khau"]);
                $ho_so = $nguoi_dung;
            }
        }
    }
} catch (PDOException $e) {
    error_log("Lỗi cơ sở dữ liệu khi đăng nhập: " . $e->getMessage());
    $loi_co_so_du_lieu = "Không kết nối được cơ sở dữ liệu. Hãy bật MySQL trong XAMPP và nhập form/database.sql.";
}

function e($gia_tri)
{
    return htmlspecialchars((string) $gia_tri, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $ho_so ? "Hồ sơ cá nhân" : "Đăng nhập"; ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="khung">
        <?php if ($ho_so) { ?>
            <h1>Hồ sơ cá nhân</h1>
            <p class="thong-bao-dung">Đăng nhập thành công lúc <?php echo e($thoi_gian_dang_nhap); ?></p>
            <table>
                <tr><th>Mã hồ sơ</th><td><?php echo e($ho_so["ma_ho_so"]); ?></td></tr>
                <tr><th>Họ và tên</th><td><?php echo e($ho_so["ho_ten"]); ?></td></tr>
                <tr><th>Ngày sinh</th><td><?php echo e(date("d/m/Y", strtotime($ho_so["ngay_sinh"]))); ?></td></tr>
                <tr><th>Giới tính</th><td><?php echo e($ho_so["gioi_tinh"]); ?></td></tr>
                <tr><th>Quốc tịch</th><td><?php echo e($ho_so["quoc_tich"]); ?></td></tr>
                <tr><th>Quê quán</th><td><?php echo e($ho_so["que_quan"]); ?></td></tr>
                <tr><th>Địa chỉ thường trú</th><td><?php echo e($ho_so["dia_chi_thuong_tru"]); ?></td></tr>
                <tr><th>Địa chỉ hiện tại</th><td><?php echo e($ho_so["dia_chi_hien_tai"]); ?></td></tr>
                <tr><th>Số điện thoại</th><td><?php echo e($ho_so["so_dien_thoai"]); ?></td></tr>
                <tr><th>Email</th><td><?php echo e($ho_so["email"]); ?></td></tr>
                <tr><th>Thời gian đăng nhập</th><td><?php echo e($thoi_gian_dang_nhap); ?></td></tr>
            </table>
            <a class="nut nut-phu" href="dang_xuat.php">Đăng xuất</a>
        <?php } else { ?>
            <h1>Đăng nhập</h1>
            <p class="thong-bao-dung">Đăng nhập để xem hồ sơ đã đăng ký.</p>

            <?php if ($loi_co_so_du_lieu != "") { ?>
                <p class="loi" role="alert"><?php echo e($loi_co_so_du_lieu); ?></p>
            <?php } ?>
            <?php if (isset($loi["dang_nhap"])) { ?>
                <p class="loi" role="alert"><?php echo e($loi["dang_nhap"]); ?></p>
            <?php } ?>

            <form method="POST" action="dang_nhap.php" autocomplete="off">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" autocomplete="off" required>
                <?php if (isset($loi["email"])) { ?><p class="loi"><?php echo e($loi["email"]); ?></p><?php } ?>

                <label for="mat_khau">Mật khẩu</label>
                <input type="password" id="mat_khau" name="mat_khau" autocomplete="new-password" required>
                <?php if (isset($loi["mat_khau"])) { ?><p class="loi"><?php echo e($loi["mat_khau"]); ?></p><?php } ?>

                <a class="quen-mat-khau" href="dang_nhap.php?quen_mat_khau=1">Quên mật khẩu?</a>
                <?php if ($hien_thong_bao_quen_mat_khau) { ?>
                    <p class="thong-bao-dung" role="status">
                        Chức năng đặt lại mật khẩu cần cấu hình gửi email và hiện chưa được bật.
                    </p>
                <?php } ?>

                <button type="submit" class="nut">Đăng nhập</button>
            </form>

            <p class="chuyen-trang">Chưa có hồ sơ? <a href="dang_ky.php">Đăng ký</a></p>
        <?php } ?>

        <p class="chuyen-trang"><a href="index.php">← Trang chủ</a></p>
    </div>
</body>
</html>
