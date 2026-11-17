# BTN 05 — Quản lý sinh viên và điểm học phần

Bài thực hành PHP + MySQL về `INSERT`, `SELECT`, `PREPARE`, kiểm tra dữ liệu và escape HTML. Form thêm **điểm học phần** gồm 3 trường bắt buộc: sinh viên, học phần, điểm (0–10). Danh sách hiển thị tên sinh viên, lớp, học phần, điểm và xếp loại.

## Cấu trúc thư mục

- `index.php` — form, validation phía PHP, `INSERT` dùng PDO prepared statement, `SELECT` + `foreach`, escape bằng `htmlspecialchars`.
- `config.php` — kết nối MySQL bằng PDO.
- `database.sql` — tạo CSDL và 4 bảng `sinhvien`, `lop`, `hocphan`, `diem`; có dữ liệu nền và để trống bảng điểm.
- `style.css` — giao diện.
- `PHAN_CONG_VA_KIEM_THU.md` — phân công 5 thành viên, kịch bản kiểm thử và luồng giải thích.

## Cách chạy

1. Cài XAMPP (Apache + MySQL) hoặc PHP 8+ và MySQL/MariaDB.
2. Mở phpMyAdmin, chọn **Import**, nhập `database.sql`. Hoặc chạy `mysql -u root -p < database.sql` trong terminal.
3. Nếu cấu hình MySQL không phải mặc định, sửa giá trị đầu tệp `config.php` hoặc đặt biến môi trường `DB_HOST`, `DB_PORT`, `DB_NAME`, `DB_USER`, `DB_PASS`.
4. Đặt thư mục `BTN_05` vào `htdocs` (XAMPP), bật Apache/MySQL, mở `http://localhost/BTN_05/`.
5. Lần đầu trang hiển thị trạng thái danh sách rỗng. Chọn sinh viên và học phần, nhập điểm rồi bấm **Lưu điểm**.

## Kiểm tra nhanh

- Điểm hợp lệ, ví dụ `8.5`: lưu thành công, xuất hiện dòng mới và xếp loại `Giỏi`.
- Thiếu sinh viên/học phần/điểm: hiện lỗi, không chạy lệnh INSERT.
- Điểm ngoài khoảng 0–10 hoặc không phải số: hiện lỗi, không lưu.
- Bảng `diem` rỗng: hiển thị thông báo “Chưa có điểm nào”.
- Sinh viên + học phần trùng: bị chặn bởi ràng buộc duy nhất và hiện thông báo phù hợp.

> Lưu ý: đây là bài thực hành nội bộ. Không đưa mật khẩu thật vào kho mã công khai; cấu hình DB có thể thay qua biến môi trường.
