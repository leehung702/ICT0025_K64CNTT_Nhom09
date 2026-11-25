<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Phương thức không được hỗ trợ.');
}
verify_csrf();
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {
    $_SESSION['flash'] = 'ID học phần không hợp lệ.';
    header('Location: index.php'); exit;
}
try {
    $stmt = $conn->prepare('DELETE FROM hocphan WHERE id = ?');
    $stmt->execute([$id]);
    $_SESSION['flash'] = $stmt->rowCount() ? 'Đã xóa học phần.' : 'Không tìm thấy học phần cần xóa.';
} catch (PDOException $e) {
    $_SESSION['flash'] = 'Không thể xóa học phần vì dữ liệu đang được bảng khác tham chiếu.';
}
header('Location: index.php');
exit;
