# Bài tập cá nhân PHP & MySQL — Quản lý Sinh viên (Bảng `sinhvien`)

## 1. Thông tin sinh viên & phân công nhóm

- **Họ và tên:** Lò Thị Huệ
- **MSSV:** 2023A1173
- **Nhóm:** Nhóm 09 (Nhiệm vụ được phân công: **Thành viên 2**)
- **Đề tài chung của nhóm:** Quản lý sinh viên và điểm học phần
- **Phần việc được nhóm phân công (`PHAN_CONG.md`):**
  - **Quản lý Sinh viên (Bảng `sinhvien`):** Xây dựng ứng dụng web CRUD cho bảng `sinhvien` (Xem danh sách, Thêm sinh viên, Sửa thông tin, Xóa/Thùng rác).
  - **Thiết kế & tài liệu kỹ thuật:** Mô tả chi tiết Chức năng 1 (Thêm/Cập nhật sinh viên, gồm cả trường hợp nhập sai); thiết kế sơ đồ CSDL (ERD) và mô tả 2 bảng `lop`, `sinhvien`; vẽ sơ đồ Lớp (Class Diagram) đối chiếu với CSDL.
  - **Phối hợp:** Thống nhất kiểu dữ liệu khóa chính/khóa ngoại với **Thành viên 1** và **Thành viên 3** (`ma_lop VARCHAR(20)`, `ma_sv VARCHAR(20)`, khóa chính `id INT UNSIGNED AUTO_INCREMENT`).

## 2. Cấu trúc thư mục nộp bài

- `index.php` — Ứng dụng PHP thực hiện đầy đủ 4 chức năng CRUD trên bảng `sinhvien` (gồm các màn hình: **Danh sách**, **Thêm sinh viên**, **Sửa**, **Thùng rác**) bằng PDO prepared statements, kiểm tra hợp lệ dữ liệu phía server, chống XSS với `htmlspecialchars` và áp dụng mô hình PRG (Post/Redirect/Get).
- `config.php` — Cấu hình kết nối MySQL qua PDO cho môi trường localhost.
- `config.example.php` — Tệp cấu hình mẫu (không chứa mật khẩu thật).
- `database.sql` — Lệnh tạo cơ sở dữ liệu riêng (`btcn_ql_sinhvien_2023a1173`), tạo bảng `lop`, `sinhvien`, các ràng buộc khóa chính/khóa ngoại/duy nhất và dữ liệu mẫu (4 sinh viên trong danh sách chính và 1 sinh viên trong thùng rác).
- `style.css` — Tệp định dạng giao diện theo bố cục thanh điều hướng và thẻ danh sách sạch gọn.
- `README.md` — Hướng dẫn cài đặt, cấu hình, chạy thử trên localhost và nhật ký kiểm thử.
- `BAOCAO_CA_NHAN.md` — Báo cáo phần việc cá nhân và quá trình sử dụng công cụ AI.

## 3. Hướng dẫn tạo CSDL, cấu hình và chạy trên localhost

1. **Khởi động môi trường:** Mở XAMPP, bật **Apache** và **MySQL**.
2. **Tạo CSDL và dữ liệu mẫu:**
   - Truy cập `http://localhost/phpmyadmin`.
   - Chọn tab **Import** (Nhập), chọn tệp `database.sql` trong thư mục '2023A1173_LoThiHue_BTCN' và nhấn **Import**.
   - Hệ thống tự tạo CSDL `btcn_ql_sinhvien_2023a1173` gồm bảng `lop` (4 lớp) và bảng `sinhvien` (5 bản ghi mẫu).
3. **Cấu hình kết nối:**
   - Sao chép thư mục `2023A1173_LoThiHue_BTCN` vào `C:\xampp\htdocs\2023A1173_LoThiHue_BTCN`.
   - Kiểm tra tệp `config.php` (hoặc sao chép từ `config.example.php` thành `config.php`) sao cho khớp thông số MySQL trên máy (`DB_HOST=127.0.0.1`, `DB_NAME=btcn_ql_sinhvien_2023a1173`, `DB_USER=root`, `DB_PASS=''`).
4. **Chạy ứng dụng:**
   - Mở trình duyệt và truy cập: `http://localhost/2023A1173_LoThiHue_BTCN/`.

## 4. Kết quả kiểm thử chức năng CRUD

| STT | Kịch bản kiểm thử | Dữ liệu nhập / Thao tác | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|---|
| 1 | Xem danh sách khi có dữ liệu | Mở trang chủ sau khi import `database.sql` | Hiển thị bảng gồm 4 sinh viên (`SV001` đến `SV004`) và mục `Thùng rác (1)` | Đạt — Hiển thị đủ 4 bản ghi theo STT `1..4` |
| 2 | Xem danh sách khi bảng rỗng | Chuyển hết các bản ghi vào thùng rác rồi xem trang Danh sách | Hiển thị thông báo danh sách sinh viên hiện đang rỗng | Đạt — Hiện khung thông báo rỗng và nút thêm mới |
| 3 | Thêm sinh viên hợp lệ | Bấm **+ Thêm sinh viên**, nhập Mã SV: `SV006`, Họ tên: `Lò Thị Huệ`, Lớp: `CNTT01` | Lưu vào bảng `sinhvien`, chuyển về Danh sách và báo thành công | Đạt — Bản ghi mới hiển thị ở cuối danh sách |
| 4 | Thêm thiếu thông tin | Để trống Họ và tên, nhấn **Lưu sinh viên** | Báo lỗi kiểm tra dữ liệu, không thêm bản ghi rỗng vào CSDL | Đạt — Báo lỗi "Họ và tên sinh viên không được để trống" |
| 5 | Thêm dữ liệu sai định dạng / trùng mã | Nhập Mã SV: `@#` hoặc nhập trùng `SV001` | Báo lỗi định dạng hoặc trùng mã sinh viên, giữ nguyên dữ liệu cũ | Đạt — Chặn lưu và hiển thị thông báo lỗi cụ thể |
| 6 | Sửa một bản ghi hợp lệ | Bấm **Sửa** ở `SV002`, đổi tên thành `Trần Quốc Bảo Anh`, đổi lớp `KTPM01`, bấm **Lưu thay đổi** | Chỉ bản ghi `SV002` được cập nhật, các dòng khác không đổi | Đạt — Cập nhật đúng bản ghi theo `WHERE id = :id` |
| 7 | Sửa với ID không tồn tại | Truy cập URL `index.php?view=edit&id=9999` | Báo không tìm thấy bản ghi, không lỗi hệ thống | Đạt — Báo "Không tìm thấy bản ghi sinh viên có ID = 9999" |
| 8 | Hủy thao tác xóa | Bấm **Xóa** ở một dòng rồi chọn **Cancel** trên hộp thoại xác nhận | Không gửi POST xóa, bản ghi vẫn còn nguyên trong danh sách | Đạt — Không thực hiện xóa |
| 9 | Xác nhận xóa & Thùng rác | Bấm **Xóa** ở `SV004` → OK; vào **Thùng rác** kiểm tra nút **Khôi phục** và **Xóa vĩnh viễn** | Xóa chuyển bản ghi vào thùng rác; Khôi phục đưa về danh sách; Xóa vĩnh viễn chạy `DELETE FROM sinhvien WHERE id = :id` | Đạt — Đúng từng bản ghi theo `id`, các bản ghi khác giữ nguyên |
| 10 | Tải lại trang (F5) sau khi Thêm/Sửa/Xóa | Nhấn F5 ngay sau khi thao tác thành công | Không gửi lại biểu mẫu POST, không tạo trùng hoặc xóa lặp | Đạt — Cơ chế Post/Redirect/Get hoạt động ổn định |
