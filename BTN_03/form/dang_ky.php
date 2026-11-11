<?php
$ho_ten = "";
$ngay_sinh = "";
$gioi_tinh = "";
$quoc_tich = "";
$que_quan = "";
$dia_chi_thuong_tru = "";
$dia_chi_hien_tai = "";
$so_dien_thoai = "";
$email = "";
$mat_khau = "";
$nhap_lai = "";
$loi = [];
$ma_ho_so = "";
$loi_co_so_du_lieu = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $ho_ten = isset($_POST["ho_ten"]) && is_string($_POST["ho_ten"]) ? trim($_POST["ho_ten"]) : "";
    $ngay_sinh = isset($_POST["ngay_sinh"]) && is_string($_POST["ngay_sinh"]) ? trim($_POST["ngay_sinh"]) : "";
    $gioi_tinh = isset($_POST["gioi_tinh"]) && is_string($_POST["gioi_tinh"]) ? $_POST["gioi_tinh"] : "";
    $quoc_tich = isset($_POST["quoc_tich"]) && is_string($_POST["quoc_tich"]) ? trim($_POST["quoc_tich"]) : "";
    $que_quan = isset($_POST["que_quan"]) && is_string($_POST["que_quan"]) ? trim($_POST["que_quan"]) : "";
    $dia_chi_thuong_tru = isset($_POST["dia_chi_thuong_tru"]) && is_string($_POST["dia_chi_thuong_tru"])
        ? trim($_POST["dia_chi_thuong_tru"]) : "";
    $dia_chi_hien_tai = isset($_POST["dia_chi_hien_tai"]) && is_string($_POST["dia_chi_hien_tai"])
        ? trim($_POST["dia_chi_hien_tai"]) : "";
    $so_dien_thoai = isset($_POST["so_dien_thoai"]) && is_string($_POST["so_dien_thoai"])
        ? trim($_POST["so_dien_thoai"]) : "";
    $email = isset($_POST["email"]) && is_string($_POST["email"]) ? trim($_POST["email"]) : "";
    $mat_khau = isset($_POST["mat_khau"]) && is_string($_POST["mat_khau"]) ? $_POST["mat_khau"] : "";
    $nhap_lai = isset($_POST["nhap_lai"]) && is_string($_POST["nhap_lai"]) ? $_POST["nhap_lai"] : "";

    if ($ho_ten == "") {
        $loi["ho_ten"] = "Vui lòng nhập họ tên.";
    }

    $ngay_sinh_hop_le = DateTime::createFromFormat("Y-m-d", $ngay_sinh);
    if (!$ngay_sinh_hop_le || $ngay_sinh_hop_le->format("Y-m-d") != $ngay_sinh
        || $ngay_sinh > date("Y-m-d")) {
        $loi["ngay_sinh"] = "Vui lòng nhập ngày sinh hợp lệ.";
    }

    if ($gioi_tinh != "Nam" && $gioi_tinh != "Nữ" && $gioi_tinh != "Khác") {
        $loi["gioi_tinh"] = "Vui lòng chọn giới tính.";
    }

    if ($quoc_tich == "") {
        $loi["quoc_tich"] = "Vui lòng nhập quốc tịch.";
    }
    if ($que_quan == "") {
        $loi["que_quan"] = "Vui lòng nhập quê quán.";
    }
    if ($dia_chi_thuong_tru == "") {
        $loi["dia_chi_thuong_tru"] = "Vui lòng nhập địa chỉ thường trú.";
    }
    if ($dia_chi_hien_tai == "") {
        $loi["dia_chi_hien_tai"] = "Vui lòng nhập địa chỉ hiện tại.";
    }

    if (!preg_match("/^[0-9+().\\s-]{7,20}$/", $so_dien_thoai)) {
        $loi["so_dien_thoai"] = "Vui lòng nhập số điện thoại hợp lệ.";
    }

    if ($email == "") {
        $loi["email"] = "Vui lòng nhập email.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $loi["email"] = "Email không đúng định dạng.";
    }

    if (strlen($mat_khau) < 6) {
        $loi["mat_khau"] = "Mật khẩu phải có ít nhất 6 ký tự.";
    }
    if ($nhap_lai != $mat_khau) {
        $loi["nhap_lai"] = "Mật khẩu nhập lại không khớp.";
    }

    if (count($loi) == 0) {
        try {
            require __DIR__ . "/ket_noi.php";

            $ma_ho_so = "HS-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(3)));
            $mat_khau_ma_hoa = password_hash($mat_khau, PASSWORD_DEFAULT);

            $sql = "INSERT INTO nguoi_dung
                (ma_ho_so, ho_ten, ngay_sinh, gioi_tinh, quoc_tich, que_quan,
                 dia_chi_thuong_tru, dia_chi_hien_tai, so_dien_thoai, email, mat_khau)
                VALUES
                (:ma_ho_so, :ho_ten, :ngay_sinh, :gioi_tinh, :quoc_tich, :que_quan,
                 :dia_chi_thuong_tru, :dia_chi_hien_tai, :so_dien_thoai, :email, :mat_khau)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ":ma_ho_so" => $ma_ho_so,
                ":ho_ten" => $ho_ten,
                ":ngay_sinh" => $ngay_sinh,
                ":gioi_tinh" => $gioi_tinh,
                ":quoc_tich" => $quoc_tich,
                ":que_quan" => $que_quan,
                ":dia_chi_thuong_tru" => $dia_chi_thuong_tru,
                ":dia_chi_hien_tai" => $dia_chi_hien_tai,
                ":so_dien_thoai" => $so_dien_thoai,
                ":email" => $email,
                ":mat_khau" => $mat_khau_ma_hoa,
            ]);
        } catch (PDOException $e) {
            error_log("Lỗi cơ sở dữ liệu khi đăng ký: " . $e->getMessage());
            if ($e->getCode() == "23000") {
                $loi["email"] = "Email này đã được đăng ký. Vui lòng dùng email khác.";
            } else {
                $loi_co_so_du_lieu = "Không lưu được thông tin. Hãy kiểm tra MySQL đã chạy và đã nhập form/database.sql.";
            }
        }
    }
}

function e($gia_tri)
{
    return htmlspecialchars($gia_tri, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Đăng ký hồ sơ</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="khung">
        <?php if ($ma_ho_so != "" && count($loi) == 0 && $loi_co_so_du_lieu == "") { ?>
            <h1>Đăng ký thành công</h1>
            <p class="thong-bao-dung">Mã hồ sơ của bạn:</p>
            <p class="ma-ho-so"><?php echo e($ma_ho_so); ?></p>
            <p class="thong-bao-dung">
                Đây là mã hồ sơ của project, không phải số CCCD. Đăng nhập để xem thông tin đã đăng ký.
            </p>
            <a class="nut" href="dang_nhap.php">Đến trang đăng nhập</a>
        <?php } else { ?>
            <h1>Đăng ký hồ sơ</h1>
            <p class="thong-bao-dung">
                Đây là project học tập, không nhập thông tin CCCD thật.
            </p>

            <?php if ($loi_co_so_du_lieu != "") { ?>
                <p class="loi" role="alert"><?php echo e($loi_co_so_du_lieu); ?></p>
            <?php } ?>

            <form method="POST" action="dang_ky.php" autocomplete="off">
                <label for="ho_ten">Họ và tên</label>
                <input type="text" id="ho_ten" name="ho_ten" maxlength="150" autocomplete="off" required
                       value="<?php echo e($ho_ten); ?>">
                <?php if (isset($loi["ho_ten"])) { ?><p class="loi"><?php echo e($loi["ho_ten"]); ?></p><?php } ?>

                <label for="ngay_sinh">Ngày sinh</label>
                <input type="date" id="ngay_sinh" name="ngay_sinh" autocomplete="off"
                       max="<?php echo date("Y-m-d"); ?>" required
                       value="<?php echo e($ngay_sinh); ?>">
                <?php if (isset($loi["ngay_sinh"])) { ?><p class="loi"><?php echo e($loi["ngay_sinh"]); ?></p><?php } ?>

                <label>Giới tính</label>
                <div class="lua-chon">
                    <?php foreach (["Nam", "Nữ", "Khác"] as $lua_chon_gioi_tinh) { ?>
                        <label>
                            <input type="radio" name="gioi_tinh" value="<?php echo e($lua_chon_gioi_tinh); ?>"
                                autocomplete="off"
                                <?php if ($gioi_tinh == $lua_chon_gioi_tinh) echo "checked"; ?> required>
                            <?php echo e($lua_chon_gioi_tinh); ?>
                        </label>
                    <?php } ?>
                </div>
                <?php if (isset($loi["gioi_tinh"])) { ?><p class="loi"><?php echo e($loi["gioi_tinh"]); ?></p><?php } ?>

                <label for="quoc_tich">Quốc tịch</label>
                <input type="text" id="quoc_tich" name="quoc_tich" maxlength="50" autocomplete="off" required
                       value="<?php echo e($quoc_tich); ?>">
                <?php if (isset($loi["quoc_tich"])) { ?><p class="loi"><?php echo e($loi["quoc_tich"]); ?></p><?php } ?>

                <label for="que_quan">Quê quán</label>
                <input type="text" id="que_quan" name="que_quan" maxlength="255" autocomplete="off" required
                       value="<?php echo e($que_quan); ?>">
                <?php if (isset($loi["que_quan"])) { ?><p class="loi"><?php echo e($loi["que_quan"]); ?></p><?php } ?>

                <label for="dia_chi_thuong_tru">Địa chỉ thường trú</label>
                <input type="text" id="dia_chi_thuong_tru" name="dia_chi_thuong_tru" maxlength="500"
                       autocomplete="off" required
                       value="<?php echo e($dia_chi_thuong_tru); ?>">
                <?php if (isset($loi["dia_chi_thuong_tru"])) { ?><p class="loi"><?php echo e($loi["dia_chi_thuong_tru"]); ?></p><?php } ?>

                <label for="dia_chi_hien_tai">Địa chỉ hiện tại</label>
                <input type="text" id="dia_chi_hien_tai" name="dia_chi_hien_tai" maxlength="500"
                       autocomplete="off" required
                       value="<?php echo e($dia_chi_hien_tai); ?>">
                <?php if (isset($loi["dia_chi_hien_tai"])) { ?><p class="loi"><?php echo e($loi["dia_chi_hien_tai"]); ?></p><?php } ?>

                <label for="so_dien_thoai">Số điện thoại</label>
                <input type="tel" id="so_dien_thoai" name="so_dien_thoai" maxlength="20"
                       autocomplete="off" required
                       value="<?php echo e($so_dien_thoai); ?>">
                <?php if (isset($loi["so_dien_thoai"])) { ?><p class="loi"><?php echo e($loi["so_dien_thoai"]); ?></p><?php } ?>

                <label for="email">Email dùng để đăng nhập</label>
                <input type="email" id="email" name="email" maxlength="190" autocomplete="off" required
                       value="<?php echo e($email); ?>">
                <?php if (isset($loi["email"])) { ?><p class="loi"><?php echo e($loi["email"]); ?></p><?php } ?>

                <label for="mat_khau">Mật khẩu</label>
                <input type="password" id="mat_khau" name="mat_khau" minlength="6"
                       autocomplete="new-password" required>
                <?php if (isset($loi["mat_khau"])) { ?><p class="loi"><?php echo e($loi["mat_khau"]); ?></p><?php } ?>

                <label for="nhap_lai">Nhập lại mật khẩu</label>
                <input type="password" id="nhap_lai" name="nhap_lai" autocomplete="new-password" required>
                <?php if (isset($loi["nhap_lai"])) { ?><p class="loi"><?php echo e($loi["nhap_lai"]); ?></p><?php } ?>

                <button type="submit" class="nut">Tạo hồ sơ</button>
            </form>

            <p class="chuyen-trang">Đã có tài khoản? <a href="dang_nhap.php">Đăng nhập</a></p>
        <?php } ?>

        <p class="chuyen-trang"><a href="index.php">← Trang chủ</a></p>
    </div>
</body>
</html>
