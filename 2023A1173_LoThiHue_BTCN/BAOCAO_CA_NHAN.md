# Báo cáo bài tập cá nhân PHP & MySQL

- **Họ và tên :** Lò Thị Huệ
- **MSSV:** 2023A1173
- **Nhóm:** Nhóm 09 — **Nhiệm vụ được phân công:** Thành viên 2
- **Đề tài chung của nhóm:** Quản lý sinh viên và điểm học phần

## 1. Phần việc được phân công và đối tượng dữ liệu quản lý

Theo bảng phân công của Nhóm 09, **Thành viên 2** phụ trách mảng **Quản lý Sinh viên (Bảng `sinhvien`)** và phối hợp với Thành viên 1, Thành viên 3 để thống nhất kiểu dữ liệu khóa chính/khóa ngoại:

1. **Ứng dụng CRUD bảng `sinhvien`:**
   - **Xem danh sách:** Truy vấn danh sách sinh viên kết hợp tên lớp (`INNER JOIN lop`), hiển thị thông báo rõ ràng khi danh sách rỗng.
   - **Thêm mới:** Kiểm tra ràng buộc mã sinh viên, họ tên, mã lớp hợp lệ trước khi `INSERT`.
   - **Sửa:** Nạp dữ liệu bản ghi theo khóa chính `id`, kiểm tra hợp lệ và cập nhật đúng 1 bản ghi qua `UPDATE sinhvien SET ... WHERE id = :id`.
   - **Xóa (kèm Thùng rác):** Xác nhận trên trình duyệt, gửi yêu cầu `POST` để chuyển vào thùng rác (`UPDATE ... SET is_deleted = 1 WHERE id = :id`), cho phép khôi phục hoặc xóa vĩnh viễn khỏi CSDL (`DELETE FROM sinhvien WHERE id = :id`).
2. **Mô tả chi tiết Chức năng 1 (Thêm / Cập nhật sinh viên, gồm cả trường hợp nhập sai):**
   - **Đầu vào:** `ma_sv` (chuỗi 3–20 ký tự `[A-Z0-9_-]`, không trùng), `ho_ten` (chuỗi 2–120 ký tự), `ma_lop` (tồn tại trong bảng `lop`).
   - **Luồng hợp lệ:** Hệ thống chuẩn hóa `ma_sv` in hoa, dùng PDO prepared statement để `INSERT` (khi thêm) hoặc `UPDATE ... WHERE id = :id` (khi sửa), sau đó chuyển hướng PRG và thông báo thành công.
   - **Các trường hợp nhập sai được kiểm soát:**
     - Để trống `ma_sv`, `ho_ten` hoặc chưa chọn `ma_lop` → báo lỗi bắt buộc nhập, không ghi vào CSDL.
     - `ma_sv` chứa ký tự đặc biệt hoặc ngắn hơn 3 ký tự → báo lỗi định dạng.
     - `ma_sv` trùng với sinh viên đã tồn tại (khi thêm kiểm tra toàn bảng; khi sửa kiểm tra `WHERE ma_sv = :ma_sv AND id <> :id`) → báo lỗi trùng mã.
     - Truyền `id` sửa không tồn tại (`?view=edit&id=9999`) → báo không tìm thấy bản ghi.
3. **Thiết kế CSDL (ERD) & mô tả 2 bảng `lop`, `sinhvien`:**
   - **Quan hệ ERD:** `lop (1) ────< (N) sinhvien` (Một lớp học có nhiều sinh viên; mỗi sinh viên thuộc về một lớp học thông qua khóa ngoại `sinhvien.ma_lop -> lop.ma_lop`, `ON UPDATE CASCADE ON DELETE RESTRICT`).
   - **Bảng `lop`:** `ma_lop VARCHAR(20) PRIMARY KEY`, `ten_lop VARCHAR(100) NOT NULL`.
   - **Bảng `sinhvien`:** `id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY`, `ma_sv VARCHAR(20) NOT NULL UNIQUE`, `ho_ten VARCHAR(120) NOT NULL`, `ma_lop VARCHAR(20) NOT NULL (FK)`, `is_deleted TINYINT(1) NOT NULL DEFAULT 0`, `ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP`.
4. **Sơ đồ Lớp (Class Diagram) đối chiếu với CSDL:**
   - Lớp `Lop`: thuộc tính `maLop: string`, `tenLop: string` (ánh xạ 1-1 với các cột bảng `lop`).
   - Lớp `SinhVien`: thuộc tính `id: int`, `maSv: string`, `hoTen: string`, `maLop: string`, `isDeleted: bool`, `ngayTao: datetime` (ánh xạ 1-1 với các cột bảng `sinhvien`) cùng các phương thức nghiệp vụ `getAll()`, `findById(id)`, `create(data)`, `update(id, data)`, `trash(id)`, `restore(id)`, `deletePermanent(id)`.

## 2. Các tệp và chức năng em tự thực hiện / chỉnh sửa

- **`database.sql`:** Tạo cơ sở dữ liệu cá nhân `btcn_ql_sinhvien_2023a1173`, định nghĩa bảng `lop` và bảng `sinhvien` đúng chuẩn kiểu dữ liệu đã thống nhất với Thành viên 1 và Thành viên 3, kèm dữ liệu mẫu để kiểm thử.
- **`config.php` & `config.example.php`:** Cấu hình kết nối PDO MySQL với `ERRMODE_EXCEPTION` và tắt `EMULATE_PREPARES`.
- **`index.php`:** Tự viết luồng xử lý 4 chức năng CRUD cho bảng `sinhvien` theo bố cục 3 tab (`Danh sách`, `Thêm sinh viên`, `Thùng rác`), hàm `e()` bọc `htmlspecialchars` chống XSS, hàm `redirectWithFlash()` chống gửi lại form khi nhấn F5.
- **`style.css`:** Thiết kế giao diện thanh điều hướng xanh đậm, thẻ danh sách và nút thao tác gọn gàng, trực quan.
- **Kiểm thử & xử lý lỗi:** Khi chạy thử chức năng Sửa và giữ nguyên `ma_sv` cũ của sinh viên, ban đầu câu lệnh kiểm tra trùng báo lỗi trùng mã; em đã sửa thành `SELECT COUNT(*) FROM sinhvien WHERE ma_sv = :ma_sv AND id <> :id` để loại trừ chính bản ghi đang cập nhật và kiểm thử lại thành công.

## 3. Việc sử dụng AI trong quá trình làm bài tập cá nhân của em

- **Công cụ sử dụng:** Gemini.
- **Câu hỏi chính đã hỏi:** Em hỏi AI cách viết 4 chức năng thêm, xem, sửa, xóa cho bảng `sinhvien` bằng PHP và PDO sao cho an toàn, cách chuyển hướng trang sau khi bấm nút để nhấn F5 không bị gửi lại dữ liệu cũ, và cách sửa lỗi báo trùng mã sinh viên khi cập nhật thông tin.
- **Phần đã tham khảo / giữ nguyên:** Em giữ lại phần kết nối CSDL trong `config.php` và giao diện chung trong `style.css` từ bài của nhóm. Với gợi ý từ AI, em tham khảo cách viết câu lệnh SQL có tham số (`prepare`, `execute`) và các điều kiện kiểm tra dữ liệu rỗng hoặc sai định dạng mã sinh viên.
- **Phần sinh viên tự chỉnh sửa và kiểm tra:** Em tự tạo CSDL riêng và sửa lại các tên bảng, tên cột trong `database.sql` cho khớp với kiểu dữ liệu đã thống nhất cùng Thành viên 1 và Thành viên 3. Ở chức năng sửa, lúc đầu em để câu lệnh kiểm tra trùng mã giống lúc thêm nên khi giữ nguyên mã cũ bị báo lỗi; em đã sửa thêm điều kiện `id <> :id` để bỏ qua chính sinh viên đang sửa. Sau đó em tự import file SQL vào phpMyAdmin trên máy và chạy thử hết các trường hợp nhập đúng, nhập thiếu, sửa và xóa từng dòng như đã ghi trong `README.md`.
