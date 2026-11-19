# BTN 06 — Quản lý sinh viên và điểm học phần (bản hoàn chỉnh)

Bài thực hành PHP + MySQL (PDO): `INSERT`, `SELECT`, **`UPDATE`**, **`DELETE` với `WHERE`**, prepared statement, kiểm tra đầu vào, xác nhận xóa và **xóa mềm (Soft Delete)** cho đối tượng **điểm học phần**.

## Tệp trong dự án

| Tệp | Vai trò |
|---|---|
| `index.php` | Trang chính: thống kê, thêm điểm, thêm sinh viên mới, thêm học phần mới, danh sách, xóa mềm, thùng rác, khôi phục |
| `edit.php` | Trang sửa điểm theo khóa chính `id` (`edit.php?id=...`) |
| `functions.php` | Hàm dùng chung: `e()` (escape), kiểm tra điểm, kiểm tra dữ liệu một dòng điểm, xếp loại |
| `config.php` | Kết nối PDO MySQL (đổi bằng biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`) |
| `database.sql` | Tạo CSDL + 4 bảng (`lop`, `sinhvien`, `hocphan`, `diem` có cột `deleted_at`) + dữ liệu nền |
| `migration_btn06.sql` | Thêm cột `deleted_at` vào CSDL BTN 05 đã có dữ liệu |
| `style.css` | Giao diện (tự đổi sáng/tối theo hệ thống) |
| `BAO_CAO_VA_KIEM_THU.md` | Phân công, luồng xử lý, kịch bản kiểm thử, báo cáo cuối buổi |

## Cách chạy (XAMPP)

1. Bật **Apache** và **MySQL** trong XAMPP.
2. Chuẩn bị CSDL — chọn **một** trong hai:
   - Bắt đầu từ đầu: phpMyAdmin → **Import** → chọn `database.sql`.
   - Đã có CSDL BTN 05 và muốn giữ dữ liệu: phpMyAdmin → chọn CSDL `quan_ly_sinh_vien` → tab **SQL** → chạy nội dung `migration_btn06.sql` (một lần; báo `Duplicate column name 'deleted_at'` nghĩa là cột đã có).
3. Chép cả thư mục vào `C:\xampp\htdocs\`, mở `http://localhost/<tên thư mục>/`.
4. Nếu MySQL không dùng cấu hình mặc định, sửa phần đầu `config.php`.

## Tính năng chính

- **Thêm điểm:** gõ tên sinh viên (có gợi ý), chọn học phần, nhập điểm 0–10.
- **Thêm sinh viên / học phần mới:** hai form riêng; sinh viên cần chọn lớp, học phần cần mã (chữ không dấu và số) và số tín chỉ. Chặn trùng tên và trùng mã.
- **Sửa:** `edit.php` nạp sẵn dữ liệu hiện tại; ID sai hoặc không tồn tại báo lỗi rõ ràng.
- **Xóa mềm:** hộp xác nhận → POST → `UPDATE diem SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id`. Dòng vẫn còn trong CSDL; tab **Thùng rác** cho phép **Khôi phục**.
- **Tìm nhanh** trong bảng, **thống kê** (số kết quả, điểm trung bình, số sinh viên, số học phần).

## Quyết định thiết kế

- Mọi `UPDATE`/`DELETE` đều có `WHERE id = :id` (khóa chính) và dùng tham số `:id`; kiểm tra `rowCount()` để biết thực sự đã đổi đúng một dòng.
- `id` là khóa chính tự tăng nên có thể không liên tục sau khi xóa/thêm; cột **STT** trong bảng chỉ để hiển thị.
- Ràng buộc `UNIQUE(ma_sv, ma_hp)` tính cả dòng ở thùng rác. Khi thêm lại đúng cặp đang ở thùng rác, hệ thống dùng lại dòng đó thay vì báo lỗi khó hiểu.
- Mọi dữ liệu đưa ra HTML đều qua `htmlspecialchars` (hàm `e()`); sau POST thành công luôn chuyển hướng để tải lại trang không gửi lặp.

## Hạn chế đã biết

- Chưa có đăng nhập và chống CSRF (ngoài phạm vi bài).
- Chưa có xóa vĩnh viễn từ thùng rác (chủ ý, để tránh mất dữ liệu).
- Tên so khớp theo collation `utf8mb4_unicode_ci` nên không phân biệt hoa thường và dấu.
