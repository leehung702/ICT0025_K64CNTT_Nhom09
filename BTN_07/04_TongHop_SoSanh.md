# 04. Tổng hợp và so sánh CRUD – API – MVC

## 1. Ba khái niệm ở ba tầng khác nhau

| | Trả lời câu hỏi | Tầng | Trong bài Quản lý Điểm |
|---|---|---|---|
| **CRUD** | Ứng dụng *làm gì* với dữ liệu? | Chức năng | Thêm/Xem/Sửa/Xóa điểm, sinh viên, môn học; khôi phục từ thùng rác |
| **API** | Các thành phần *giao tiếp* thế nào? | Giao tiếp | Endpoint HTTP: GET để xem, POST + `action` để thay đổi |
| **MVC** | Mã nguồn được *tổ chức* ra sao? | Kiến trúc | Model (PDO, SQL, hàm nghiệp vụ), View (HTML/CSS/JS), Controller (khối xử lý POST) |

## 2. Luồng xử lý tổng hợp: chức năng "Thêm điểm"

```
1. Người dùng điền form (View) và bấm "Lưu"
      │
2. Trình duyệt gửi  POST index.php  (action=create, csrf, sinhvien_id, hocphan_id, ...)   ← API
      │
3. Controller: kiểm tra phiên đăng nhập → csrf_ok() → validate (doc_id, parse_diem)
      │         → kiểm tra sinh viên/học phần tồn tại → kiểm tra không trùng
      │
4. Model: tinh_diem_tk() + xep_loai() → INSERT INTO diem                                  ← CRUD: Create
      │
5. flash_set('ok', ...) → redirect về index.php (Post/Redirect/Get)
      │
6. Controller chạy SELECT … JOIN … WHERE deleted_at IS NULL                               ← CRUD: Read
      │
7. View in bảng điểm + hộp thông báo (toast) cho người dùng
```

## 3. Bảng đối chiếu theo từng chức năng

| Chức năng | CRUD | Endpoint (API) | Thành phần MVC chính |
|---|---|---|---|
| Thêm điểm | Create | `POST index.php` `action=create` | Controller → Model → View |
| Tra cứu điểm | Read | `GET index.php?q=…&hp=…` | Controller → Model → View |
| Sửa / nhập điểm | Update | `POST index.php` `action=update` | Controller → Model |
| Khóa điểm | Update (đổi trạng thái) | `POST index.php` `action=lock` | Controller → Model |
| Xóa điểm | Delete (mềm) | `POST index.php` `action=delete` | Controller → Model |
| Khôi phục | Update (`deleted_at = NULL`) | `POST trash.php` `action=restore*` | Controller → Model |

## 4. Câu hỏi thảo luận và trả lời của nhóm

**Câu 1. CRUD, API và MVC có thay thế nhau được không?**
Không. Chúng thuộc ba tầng khác nhau (chức năng, giao tiếp, kiến trúc) và cùng có mặt trong một ứng dụng.

**Câu 2. Bài của nhóm đã có API chưa?**
Có các endpoint HTTP dạng form (GET/POST + `action`), nhưng chưa có REST API trả JSON. Có thể bổ sung `api/diem.php` mà không phải viết lại nghiệp vụ.

**Câu 3. Bài của nhóm có phải MVC không?**
Chưa chuẩn. Các thành phần M, V, C đã có nhưng gộp chung trong từng tệp; CSS/JS và hàm nghiệp vụ đã tách riêng.

**Câu 4. Vì sao chọn xóa mềm thay vì xóa cứng?**
Để khôi phục được khi xóa nhầm và giữ toàn vẹn dữ liệu (khóa ngoại `RESTRICT`, điểm đã khóa không bị xóa).

**Câu 5. Phần nào của bài vượt ra ngoài CRUD thuần?**
Tính điểm tổng kết, xếp loại, vòng đời trạng thái (Chưa nhập → Đã nhập → Đã khóa) và các ràng buộc chặn sửa/xóa điểm đã khóa.

## 5. Kết luận chung

- Bài Quản lý Điểm cài đặt đầy đủ **CRUD** cho điểm, sinh viên, môn học, có xóa mềm và khôi phục.
- Chức năng được cung cấp qua các **endpoint HTTP** có xác thực, chống CSRF, chống SQL Injection, chống XSS; chưa có API JSON.
- Mã nguồn có đủ ba vai trò của **MVC** nhưng chưa tách thành tệp riêng; hướng phát triển là tách `models/`, `controllers/`, `views/` và bổ sung `api/`.

## 6. Hướng phát triển

1. Tách mã nguồn theo cấu trúc MVC (xem `03_MVC.md`).
2. Thêm API JSON cho điểm, sinh viên, môn học (xem `02_API.md`).
3. Chuyển tài khoản viết cứng sang bảng `users` với `password_hash()`.
4. Đổi đăng xuất từ GET sang POST có token CSRF.
5. Gom các lệnh `ALTER TABLE` tự động vào tệp `database.sql` hoặc tệp migration.
