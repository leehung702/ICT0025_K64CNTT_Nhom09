# 03. MVC – Kết quả thảo luận

## 1. Khái niệm

**MVC (Model – View – Controller)** là mẫu kiến trúc chia ứng dụng thành ba thành phần với trách nhiệm riêng:

| Thành phần | Trách nhiệm |
|---|---|
| **Model** | Quản lý dữ liệu và logic nghiệp vụ: truy cập CSDL, kiểm tra hợp lệ, quy tắc xử lý |
| **View** | Hiển thị cho người dùng (HTML, giao diện), không xử lý nghiệp vụ |
| **Controller** | Nhận yêu cầu, gọi Model xử lý, chọn View phù hợp để trả kết quả |

### Luồng hoạt động

```
Người dùng → Controller → Model (truy vấn DB)
                ↓              ↓
              View  ←  dữ liệu trả về
                ↓
           Người dùng thấy kết quả
```

## 2. Ưu điểm và nhược điểm

**Ưu điểm**
- Tách biệt mối quan tâm (separation of concerns): mã gọn, dễ bảo trì.
- Nhiều người làm song song (người làm giao diện, người làm logic).
- Dễ kiểm thử, dễ tái sử dụng (một Model dùng cho nhiều View).

**Nhược điểm**
- Dự án nhỏ có thể thừa và phức tạp hơn cần thiết.
- Dễ đặt logic sai chỗ nếu thiếu quy ước (ví dụ "Controller béo").

Framework dùng MVC: Laravel (PHP), Spring MVC (Java), ASP.NET MVC (C#), Django (Python, biến thể MTV), Ruby on Rails.

## 3. Đối chiếu với bài Quản lý Điểm

Bài **chưa phải MVC chuẩn**. Mỗi tệp PHP vừa nhận request, vừa truy vấn CSDL, vừa in HTML (mô hình *page controller*). Tuy nhiên, các thành phần của MVC đã xuất hiện:

| Thành phần | Hiện có trong bài | Mức độ tách |
|---|---|---|
| **Model** | `config.php` (kết nối PDO); các câu SQL; `functions.php` (`tinh_diem_tk`, `xep_loai`, `parse_diem`); cấu trúc bảng và ràng buộc trong `database.sql` | Logic tính toán đã tách; câu SQL còn nằm trong tệp trang |
| **View** | Phần HTML cuối mỗi tệp; `style.css`; JavaScript giao diện (`action-ui.js`: hộp thoại xác nhận + toast; `hero-carousel.js`, `carousel.js`) | Đã tách CSS và JS; HTML còn xen PHP |
| **Controller** | Khối `if ($_SERVER['REQUEST_METHOD'] === 'POST')` ở đầu mỗi tệp: kiểm tra CSRF, validate, gọi SQL, `flash_set`, `header('Location')` | Chưa tách, nằm cùng tệp với View |

### Hạn chế hiện tại
- Câu SQL và HTML nằm chung trong một tệp, khó tái sử dụng.
- Các đoạn tự thêm cột `deleted_at` bằng `ALTER TABLE` lặp lại ở nhiều trang.
- Tài khoản đăng nhập viết cứng trong `login.php`.

## 4. Đề xuất tổ chức lại theo MVC

```
project/
├── index.php              # router: nhận request, gọi controller
├── config/
│   └── config.php
├── models/
│   ├── Diem.php           # create, update, lock, softDelete, search...
│   ├── SinhVien.php
│   └── HocPhan.php
├── controllers/
│   ├── DiemController.php
│   ├── SinhVienController.php
│   ├── HocPhanController.php
│   └── TrashController.php
├── views/
│   ├── diem/list.php
│   ├── sinhvien/list.php
│   ├── hocphan/list.php
│   └── trash/list.php
├── helpers/
│   └── functions.php
└── public/
    ├── css/style.css
    └── js/action-ui.js
```

### Ví dụ tách một chức năng

```php
// models/Diem.php
class Diem {
    public function __construct(private PDO $pdo) {}

    public function softDelete(int $id): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE diem SET deleted_at = NOW()
              WHERE id = :id AND trang_thai <> 'Da_khoa' AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount() === 1;
    }
}

// controllers/DiemController.php
class DiemController {
    public function __construct(private Diem $diem) {}

    public function delete(): void {
        $id = doc_id($_POST['id'] ?? null);
        $ok = $id !== null && $this->diem->softDelete($id);
        flash_set($ok ? 'ok' : 'error', $ok ? 'Đã chuyển vào Thùng rác.' : 'Không xóa được.');
        header('Location: index.php');
        exit;
    }
}
```

View chỉ nhận dữ liệu đã chuẩn bị và in ra bằng `e()`, không chứa câu SQL.

## 5. Kết luận thảo luận

1. Bài có đủ ba thành phần Model, View, Controller nhưng đang **trộn trong cùng một tệp**.
2. Lớp nghiệp vụ (`functions.php`) và giao diện (CSS/JS) đã tách khá tốt, là nền tảng thuận lợi để chuyển sang MVC.
3. Với quy mô hiện tại, mô hình page controller vẫn chạy ổn; nếu mở rộng thêm chức năng hoặc làm việc nhóm đông người thì nên tách theo cấu trúc đề xuất.
