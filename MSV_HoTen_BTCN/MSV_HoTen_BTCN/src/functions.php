<?php
// Hàm dùng chung cho chức năng quản lý điểm học phần.

// Quy tắc tính điểm (cần thống nhất với Thành viên 5).
const TY_LE_QT  = 0.4;   // điểm quá trình 40%
const TY_LE_THI = 0.6;   // điểm thi 60%

// Escape nội dung trước khi in ra HTML (chống XSS).
function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function chuyen_huong($url) {
    header('Location: ' . $url);
    exit;
}

// Lấy một trường POST dạng chuỗi (bỏ qua nếu là mảng).
function post_str($key) {
    return (isset($_POST[$key]) && is_string($_POST[$key])) ? trim($_POST[$key]) : '';
}

function tinh_diem_tk($qt, $thi) {
    return round(TY_LE_QT * $qt + TY_LE_THI * $thi, 1);
}

function xep_loai($tk) {
    if ($tk >= 8.5) return 'A';
    if ($tk >= 7.0) return 'B';
    if ($tk >= 5.5) return 'C';
    if ($tk >= 4.0) return 'D';
    return 'F';
}

function nhan_trang_thai($tt) {
    $map = ['Chua_nhap' => 'Chưa nhập', 'Da_nhap' => 'Đã nhập', 'Da_khoa' => 'Đã khóa'];
    return $map[$tt] ?? $tt;
}

// Đọc và kiểm tra một ô điểm. Trả về [giá trị hoặc null, thông báo lỗi hoặc null].
function doc_diem($raw, $ten) {
    $raw = trim(str_replace(',', '.', (string)$raw));
    if ($raw === '') {
        return [null, $ten . ' không được để trống.'];
    }
    if (!preg_match('/^-?\d+(\.\d{1,2})?$/', $raw)) {
        return [null, $ten . ' phải là số (tối đa 2 chữ số thập phân).'];
    }
    $v = (float)$raw;
    if ($v < 0 || $v > 10) {
        return [null, $ten . ' phải nằm trong khoảng từ 0 đến 10.'];
    }
    return [$v, null];
}

// Kiểm tra một id có tồn tại không (câu SQL truyền vào là hằng cố định trong mã).
function ton_tai($conn, $sql, $id) {
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        return false;
    }
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $co = $res && $res->num_rows > 0;
    $stmt->close();
    return $co;
}

// Kiểm tra toàn bộ dữ liệu form nhập/sửa điểm.
function kiem_tra_diem($conn, $in) {
    $loi = [];

    $sv = filter_var($in['sinhvien_id'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $hp = filter_var($in['hocphan_id']  ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($sv === false) {
        $loi[] = 'Vui lòng chọn sinh viên.';
    } elseif (!ton_tai($conn, 'SELECT id FROM sinhvien WHERE id = ?', $sv)) {
        $loi[] = 'Sinh viên đã chọn không tồn tại.';
    }

    if ($hp === false) {
        $loi[] = 'Vui lòng chọn học phần.';
    } elseif (!ton_tai($conn, 'SELECT id FROM hocphan WHERE id = ?', $hp)) {
        $loi[] = 'Học phần đã chọn không tồn tại.';
    }

    list($qt,  $e1) = doc_diem($in['diem_qt']  ?? '', 'Điểm quá trình');
    list($thi, $e2) = doc_diem($in['diem_thi'] ?? '', 'Điểm thi');
    if ($e1) { $loi[] = $e1; }
    if ($e2) { $loi[] = $e2; }

    return ['loi' => $loi, 'sv' => $sv, 'hp' => $hp, 'qt' => $qt, 'thi' => $thi];
}

// Lấy danh sách dòng từ một câu SELECT cố định (không có tham số từ người dùng).
function lay_danh_sach($conn, $sql) {
    $res = $conn->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
}
