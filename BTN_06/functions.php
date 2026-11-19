<?php
declare(strict_types=1);

/** Escape dữ liệu trước khi đưa vào HTML (chống chèn HTML/script). */
function e(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function classification(float $score): string
{
    if ($score >= 8.5) return 'Giỏi';
    if ($score >= 7.0) return 'Khá';
    if ($score >= 5.0) return 'Trung bình';
    return 'Yếu';
}

/** Tên lớp CSS tương ứng với xếp loại (dùng để tô màu huy hiệu). */
function classificationClass(float $score): string
{
    if ($score >= 8.5) return 'grade-a';
    if ($score >= 7.0) return 'grade-b';
    if ($score >= 5.0) return 'grade-c';
    return 'grade-d';
}

/** Chữ cái đầu của tên (từ cuối trong họ tên) để hiện ảnh đại diện chữ. */
function initials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name), -1, PREG_SPLIT_NO_EMPTY);
    if (!$parts) {
        return '?';
    }
    $last = end($parts);
    return mb_strtoupper(mb_substr($last, 0, 1));
}

/** Bỏ khoảng trắng thừa ở đầu/cuối và giữa các từ. */
function normalizeName(string $value): string
{
    return trim((string)preg_replace('/\s+/u', ' ', $value));
}

/** Trả về thông báo lỗi nếu điểm sai, null nếu hợp lệ. */
function validateScore(string $score): ?string
{
    if ($score === '') {
        return 'Vui lòng nhập điểm.';
    }
    if (!preg_match('/^\d{1,2}(\.\d{1,2})?$/', $score) || (float)$score > 10) {
        return 'Điểm phải là số trong khoảng từ 0 đến 10 (tối đa 2 chữ số thập phân).';
    }
    return null;
}

/**
 * Kiểm tra dữ liệu một dòng điểm (dùng chung cho thêm và sửa):
 * tên sinh viên phải khớp đúng một sinh viên, học phần phải tồn tại, điểm hợp lệ.
 *
 * @return array{0: string[], 1: ?string} [danh sách lỗi, mã sinh viên tìm được]
 */
function validateGradeFields(PDO $pdo, string $studentName, string $courseCode, string $score): array
{
    $errors = [];
    $studentId = null;

    if ($studentName === '') {
        $errors[] = 'Vui lòng nhập tên sinh viên.';
    } elseif (mb_strlen($studentName) > 120) {
        $errors[] = 'Tên sinh viên tối đa 120 ký tự.';
    }
    if ($courseCode === '') {
        $errors[] = 'Vui lòng chọn học phần.';
    }
    $scoreError = validateScore($score);
    if ($scoreError !== null) {
        $errors[] = $scoreError;
    }
    if ($errors) {
        return [$errors, null];
    }

    $findStudent = $pdo->prepare('SELECT ma_sv FROM sinhvien WHERE ho_ten = :ho_ten ORDER BY ma_sv');
    $findStudent->execute(['ho_ten' => $studentName]);
    $matches = $findStudent->fetchAll();
    if (count($matches) === 0) {
        $errors[] = 'Không tìm thấy sinh viên "' . $studentName . '". Hãy chọn tên trong gợi ý hoặc thêm sinh viên mới.';
    } elseif (count($matches) > 1) {
        $errors[] = 'Có nhiều sinh viên trùng tên "' . $studentName . '"; không thể xác định chính xác.';
    } else {
        $studentId = (string)$matches[0]['ma_sv'];
    }

    $checkCourse = $pdo->prepare('SELECT COUNT(*) FROM hocphan WHERE ma_hp = :ma_hp');
    $checkCourse->execute(['ma_hp' => $courseCode]);
    if ((int)$checkCourse->fetchColumn() === 0) {
        $errors[] = 'Học phần được chọn không tồn tại.';
    }

    return [$errors, $studentId];
}
