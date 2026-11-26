# 02. API – Kết quả thảo luận

## 1. Khái niệm

**API (Application Programming Interface)** là giao diện cho phép một phần mềm giao tiếp với phần mềm khác mà không cần biết chi tiết bên trong. Có thể hình dung như người phục vụ trong nhà hàng: khách gọi món (request), bếp xử lý phía sau, khách nhận món (response).

Trong lập trình web, **Web API** là tập các *endpoint* mà client (trình duyệt, ứng dụng di động) gọi tới server.

## 2. REST API

**REST** là kiểu thiết kế API phổ biến nhất:
- Dùng **HTTP method** để biểu diễn thao tác, **URL** để biểu diễn tài nguyên.
- **Stateless**: mỗi request tự chứa đủ thông tin, server không nhớ trạng thái giữa các request.
- Dữ liệu trao đổi thường ở dạng **JSON**.

```
GET    /api/diem        → lấy danh sách điểm
GET    /api/diem/5      → lấy điểm có id = 5
POST   /api/diem        → thêm điểm mới
PUT    /api/diem/5      → cập nhật điểm 5
DELETE /api/diem/5      → xóa điểm 5
```

| Mã HTTP | Ý nghĩa |
|---|---|
| 200 | Thành công |
| 201 | Đã tạo |
| 400 | Yêu cầu sai |
| 401 | Chưa xác thực |
| 404 | Không tìm thấy |
| 500 | Lỗi server |

## 3. Hiện trạng trong bài Quản lý Điểm và học phần

Bài là ứng dụng **server-side rendering**: trình duyệt gửi form, server xử lý rồi trả về HTML hoặc chuyển hướng. Do đó bài **chưa có REST API trả JSON**.

Tuy vậy, mỗi trang vẫn hoạt động như một tập **endpoint HTTP**, phân biệt bằng method và tham số `action`:

| Endpoint | Method | Tham số | Chức năng |
|---|---|---|---|
| `index.php` | GET | `q, hp, xl, tt, hk, nh` | Read: tra cứu có lọc |
| `index.php?edit=ID` | GET | `edit` | Mở chế độ sửa / nhập điểm |
| `index.php` | POST | `action=create` | Create điểm |
| `index.php` | POST | `action=update` | Update điểm |
| `index.php` | POST | `action=lock` | Khóa điểm |
| `index.php` | POST | `action=delete` | Xóa mềm điểm |
| `student.php`, `subject.php` | POST | `action=create/update/delete` | CRUD sinh viên, môn học |
| `trash.php` | POST | `action=restore / restore_student / restore_subject` | Khôi phục dữ liệu |
| `login.php` | POST | `username, password, csrf` | Đăng nhập |
| `index.php?logout=1` | GET | `logout` | Đăng xuất |

### Cơ chế bảo vệ và phản hồi đã có

| Cơ chế | Cách thực hiện trong bài |
|---|---|
| Xác thực | Session `$_SESSION['dang_nhap']`; chưa đăng nhập thì chuyển về `login.php` |
| Chống CSRF | `csrf_token()` sinh token, `csrf_ok()` kiểm tra bằng `hash_equals` |
| Chống SQL Injection | Prepared statements (PDO, `EMULATE_PREPARES = false`) |
| Chống XSS | Hàm `e()` bọc `htmlspecialchars` khi in dữ liệu |
| Phản hồi | Mẫu **Post/Redirect/Get** kèm thông báo flash (`flash_set`, `flash_get`) |
| Session an toàn | `session_regenerate_id(true)` sau khi đăng nhập |

## 4. So sánh hai cách phản hồi

| Tiêu chí | Hiện tại (HTML + redirect) | REST API (JSON) |
|---|---|---|
| Đối tượng dùng | Người dùng qua trình duyệt | Mọi client (web, mobile, hệ thống khác) |
| Kết quả trả về | Trang HTML / chuyển hướng | JSON + mã trạng thái HTTP |
| Tách frontend – backend | Không | Có |
| Độ phức tạp | Thấp | Cao hơn |

## 5. Đề xuất mở rộng: thêm API JSON

Tạo thư mục `api/` và dùng lại các hàm trong `functions.php`. Ví dụ rút gọn:

```php
<?php
// api/diem.php (mẫu đề xuất)
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');

if (($_SESSION['dang_nhap'] ?? false) !== true) {
    http_response_code(401);
    echo json_encode(['error' => 'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
    exit;
}
require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../config.php';

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':      // danh sách điểm chưa bị xóa
        $rows = $pdo->query('SELECT id, sinhvien_id, hocphan_id, diem_tk, xep_loai, trang_thai
                               FROM diem WHERE deleted_at IS NULL')->fetchAll();
        echo json_encode($rows, JSON_UNESCAPED_UNICODE);
        break;

    case 'DELETE':   // xóa mềm, không xóa điểm đã khóa
        $id = doc_id($_GET['id'] ?? null);
        $stmt = $pdo->prepare("UPDATE diem SET deleted_at = NOW()
                                WHERE id = :id AND trang_thai <> 'Da_khoa' AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        http_response_code($stmt->rowCount() === 1 ? 200 : 404);
        echo json_encode(['deleted' => $stmt->rowCount() === 1]);
        break;

    default:
        http_response_code(405);
}
```

## 6. Kết luận thảo luận

1. Bài hiện dùng mô hình form + redirect, chưa có REST API trả JSON.
2. Các trang vẫn là tập endpoint HTTP có xác thực, chống CSRF và dùng prepared statements.
3. Có thể bổ sung `api/diem.php` mà không phải viết lại logic, vì các hàm nghiệp vụ đã tách trong `functions.php`.
