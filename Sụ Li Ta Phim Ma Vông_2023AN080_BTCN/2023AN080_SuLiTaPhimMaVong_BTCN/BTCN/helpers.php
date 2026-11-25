<?php
// ===== Quy tắc nghiệp vụ (thống nhất với Thành viên 5) =====
const TRONG_SO_QT  = 0.4;   // điểm quá trình chiếm 40%
const TRONG_SO_THI = 0.6;   // điểm thi chiếm 60%

// Ngắn gọn cho htmlspecialchars: dùng MỌI LẦN in dữ liệu ra HTML để chống XSS.
function h($s): string {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

// Điểm tổng kết = qt*40% + thi*60%, làm tròn 1 chữ số thập phân.
function tinh_diem_tk(float $qt, float $thi): float {
    return round($qt * TRONG_SO_QT + $thi * TRONG_SO_THI, 1);
}

// Xếp loại chữ theo thang: A>=8.5, B>=7.0, C>=5.5, D>=4.0, còn lại F.
function xep_loai(float $tk): string {
    if ($tk >= 8.5) return 'A';
    if ($tk >= 7.0) return 'B';
    if ($tk >= 5.5) return 'C';
    if ($tk >= 4.0) return 'D';
    return 'F';
}

// Đọc 1 ô điểm người dùng gõ. Trả về:
//   null  -> ô để trống (hoặc không hợp lệ: khi đó đã thêm lỗi vào $errors)
//   float -> điểm hợp lệ trong khoảng 0..10
function doc_diem(string $raw, string $nhan, array &$errors): ?float {
    $raw = trim(str_replace(',', '.', $raw));      // cho phép gõ 7,5 kiểu Việt Nam
    if ($raw === '') {
        return null;
    }
    if (!is_numeric($raw)) {
        $errors[] = "$nhan phải là một số (bạn nhập: $raw).";
        return null;
    }
    $v = (float)$raw;
    if ($v < 0) {
        $errors[] = "$nhan không được âm (bạn nhập: $raw).";
        return null;
    }
    if ($v > 10) {
        $errors[] = "$nhan không được lớn hơn 10 (bạn nhập: $raw).";
        return null;
    }
    return $v;
}

// Từ hai điểm đã đọc -> [diem_tk, xep_loai, trang_thai]. Có thể thêm lỗi vào $errors.
function tong_ket(?float $qt, ?float $thi, array &$errors): array {
    if ($qt === null && $thi === null) {
        return [null, null, 'Chua_nhap'];          // chưa có điểm nào
    }
    if ($qt === null || $thi === null) {
        $errors[] = "Hãy nhập đủ cả hai điểm, hoặc để trống cả hai.";
        return [null, null, 'Chua_nhap'];
    }
    $tk = tinh_diem_tk($qt, $thi);
    return [$tk, xep_loai($tk), 'Da_nhap'];
}

function hien_diem($v): string {
    return ($v === null) ? '—' : h(number_format((float)$v, 1));
}

// Thông báo ngắn sau khi chuyển trang (?msg=...). Chỉ nhận các mã có sẵn.
function thong_bao(string $code): string {
    $ds = [
        'them'     => 'Đã thêm điểm.',
        'sua'      => 'Đã cập nhật điểm.',
        'xoa'      => 'Đã xóa bản ghi.',
        'khoa'     => 'Đã khóa điểm.',
        'khongxoa' => 'Không xóa được (không tồn tại hoặc điểm đã khóa).',
        'khongkhoa'=> 'Không khóa được (chỉ khóa điểm ở trạng thái Da_nhap).',
    ];
    return $ds[$code] ?? '';
}

function ve_dau_trang(string $tieude): void { ?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= h($tieude) ?></title>
<style>
 body{font-family:Arial,sans-serif;margin:24px;max-width:960px}
 table{border-collapse:collapse;width:100%} th,td{border:1px solid #999;padding:6px 8px;text-align:left}
 th{background:#eee} .loi{color:#b00020} .ok{color:#0a7a2f} form.inline{display:inline}
 label{display:block;margin:8px 0 2px}
</style>
</head>
<body>
<h1><?= h($tieude) ?></h1>
<?php }

function ve_cuoi_trang(): void { ?>
</body>
</html>
<?php }
