<?php
declare(strict_types=1);

/* Hàm dùng chung cho bài CRUD "Quản lý Điểm & Xếp loại" (Thành viên 5). */

const TRANG_THAI = [
    'Chua_nhap' => 'Chưa nhập',
    'Da_nhap'   => 'Đã nhập',
    'Da_khoa'   => 'Đã khóa',
];
const XEP_LOAI = ['A', 'B', 'C', 'D', 'F'];

// Công thức điểm tổng kết (thống nhất với Thành viên 4).
const TRONG_SO_QT  = 0.4;
const TRONG_SO_THI = 0.6;

/** Escape dữ liệu trước khi in ra HTML. */
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Đọc một ô điểm do người dùng nhập.
 * @return array{0: ?float, 1: ?string} [giá trị, thông báo lỗi]; ô trống -> [null, null]
 */
function parse_diem(string $raw, string $nhan): array
{
    $s = str_replace(',', '.', trim($raw));
    if ($s === '') {
        return [null, null];
    }
    if (!preg_match('/^[+-]?\d+(\.\d+)?$/', $s)) {
        return [null, "$nhan phải là một số (ví dụ 7.5)."];
    }
    $v = (float)$s;
    if ($v < 0) {
        return [null, "$nhan không được âm."];
    }
    if ($v > 10) {
        return [null, "$nhan không được lớn hơn 10."];
    }
    if (isset(explode('.', $s)[1]) && strlen(rtrim(explode('.', $s)[1], '0')) > 2) {
        return [null, "$nhan chỉ được tối đa 2 chữ số thập phân."];
    }
    return [round($v, 2), null];
}

/** Điểm tổng kết = 0.4 * quá trình + 0.6 * thi, làm tròn 2 chữ số. */
function tinh_diem_tk(float $qt, float $thi): float
{
    return round(TRONG_SO_QT * $qt + TRONG_SO_THI * $thi, 2);
}

/** Xếp loại theo điểm tổng kết: A >= 8.5, B >= 7.0, C >= 5.5, D >= 4.0, còn lại F. */
function xep_loai(float $tk): string
{
    if ($tk >= 8.5) return 'A';
    if ($tk >= 7.0) return 'B';
    if ($tk >= 5.5) return 'C';
    if ($tk >= 4.0) return 'D';
    return 'F';
}

/** Định dạng điểm để hiển thị; NULL -> dấu gạch. */
function hien_diem(mixed $v): string
{
    return $v === null ? '—' : number_format((float)$v, 2, '.', '');
}

/** Đọc ID dương từ chuỗi; không hợp lệ -> null. */
function doc_id(mixed $raw): ?int
{
    $id = filter_var($raw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    return $id === false ? null : (int)$id;
}

/** Thoát ký tự đặc biệt của LIKE (dùng cùng ESCAPE '!'). */
function like_escape(string $s): string
{
    return str_replace(['!', '%', '_'], ['!!', '!%', '!_'], $s);
}

/* ---------- CSRF + thông báo flash (lưu trong session) ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf'])
        && is_string($_POST['csrf'])
        && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

function flash_set(string $loai, string $noiDung): void
{
    $_SESSION['flash'] = ['loai' => $loai, 'noi_dung' => $noiDung];
}

function flash_get(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $f;
}
