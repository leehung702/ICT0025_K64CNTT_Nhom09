# BTCN - Quản lý điểm học phần (bảng `diem`)

Thành viên 4. Bài CRUD cá nhân: Thêm, Xem, Sửa, Xóa điểm học phần, có tính điểm tổng kết và xếp loại.

## Yêu cầu môi trường
XAMPP (Apache, PHP 8.x, MySQL/MariaDB), trình duyệt web.

## Cách chạy
1. Bật **MySQL** rồi **Apache** trong XAMPP Control Panel.
2. Chép thư mục này vào `htdocs` (ví dụ `C:\xampp\htdocs\MSV_HoTen_BTCN`).
3. Vào `http://localhost/phpmyadmin`, tab **Import**, chọn `database.sql`, bấm Import (tạo CSDL `qldiem` cùng 4 bảng và dữ liệu mẫu).
4. Sao chép `config.example.php` thành `config.php`; nếu MySQL của bạn có mật khẩu thì điền vào `pass`.
5. Mở `http://localhost/MSV_HoTen_BTCN/src/index.php`.
   Nếu Apache dùng cổng khác 80, thêm số cổng, ví dụ `http://localhost:8080/...`.

Không nộp và không đẩy `config.php` lên GitHub (đã có trong `.gitignore`).

## Cấu trúc
| Tệp | Vai trò |
|-----|---------|
| `database.sql` | Tạo 4 bảng `lop`, `sinhvien`, `hocphan`, `diem` (khóa chính, khóa ngoại, ràng buộc điểm 0 đến 10) và dữ liệu mẫu |
| `config.example.php` | Mẫu cấu hình kết nối (không có mật khẩu thật) |
| `src/index.php` | Xem danh sách (SELECT + JOIN, vòng lặp `foreach`) |
| `src/them.php` | Thêm điểm (validate, INSERT bằng prepared statement) |
| `src/sua.php` | Sửa điểm (UPDATE bằng prepared statement) |
| `src/xoa.php` | Xóa điểm (chỉ nhận form POST theo `id`) |
| `src/functions.php` | Hàm dùng chung: kiểm tra điểm, tính điểm tổng kết, xếp loại, escape HTML |
| `src/form_diem.php`, `header.php`, `footer.php` | Giao diện dùng chung |

## Quy tắc nghiệp vụ
- Điểm tổng kết = 0,4 × điểm quá trình + 0,6 × điểm thi, làm tròn 1 chữ số thập phân.
- Xếp loại: A từ 8,5; B từ 7,0; C từ 5,5; D từ 4,0; F dưới 4,0.
- Trạng thái: `Chua_nhap`, `Da_nhap`, `Da_khoa`. Điểm `Da_khoa` không được sửa hoặc xóa.
- Mỗi sinh viên chỉ có một dòng điểm cho mỗi học phần.

## Bảo mật
Mọi truy vấn có dữ liệu nhập đều dùng prepared statement; dữ liệu in ra HTML đều qua `htmlspecialchars()`; xóa chỉ bằng form POST theo `id`; `id` trên URL được ép kiểu số nguyên.

## Nhật ký kiểm thử
Chạy từng ca trên máy của bạn, rồi điền cột "Kết quả thực tế" (Đạt hoặc Không đạt kèm ghi chú).

| # | Trường hợp | Đầu vào | Kết quả mong đợi | Kết quả thực tế |
|---|-----------|---------|------------------|-----------------|
| 1 | Nhập hợp lệ | Chọn SV, HP chưa có điểm; QT 8; Thi 9 | Về danh sách, thông báo thành công; TK 8.6, xếp loại A, trạng thái Đã nhập | |
| 2 | Để trống điểm thi | QT 8; Thi để trống | Báo lỗi "không được để trống", không thêm dòng | |
| 3 | Điểm âm | QT -1; Thi 7 | Báo lỗi "từ 0 đến 10", không thêm dòng | |
| 4 | Điểm lớn hơn 10 | QT 7; Thi 11 | Báo lỗi "từ 0 đến 10", không thêm dòng | |
| 5 | Nhập chữ | QT `abc` | Báo lỗi "phải là số", không thêm dòng | |
| 6 | Điểm biên | QT 0, Thi 0 rồi QT 10, Thi 10 | Chấp nhận; TK 0.0 (F) và 10.0 (A) | |
| 7 | Chưa chọn sinh viên | Bỏ trống ô sinh viên | Báo lỗi "chọn sinh viên", giữ lại dữ liệu đã nhập | |
| 8 | Trùng sinh viên và học phần | Chọn cặp đã có điểm | Báo đã có điểm, không thêm dòng | |
| 9 | Sửa điểm | Dòng 1: đổi Thi từ 9 thành 10 | TK 9.2, xếp loại A | |
| 10 | Sửa điểm đã khóa | Mở `sua.php?id=4` | Về danh sách, báo "đã khóa, không thể sửa" | |
| 11 | Xóa điểm chưa khóa | Bấm Xóa trên dòng Đã nhập, xác nhận | Dòng bị xóa, báo đã xóa | |
| 12 | Xóa điểm đã khóa | Gửi POST `xoa.php` với `id=4` | Không xóa, báo "đã khóa, không thể xóa" | |
| 13 | Mở `xoa.php` bằng GET | Gõ `.../src/xoa.php?id=1` | Không xóa, quay về danh sách | |
| 14 | `id` sai trên URL | `sua.php?id=abc` và `sua.php?id=1 OR 1=1` | Quay về danh sách, báo lỗi, không lỗi SQL | |
| 15 | Danh sách rỗng | `TRUNCATE TABLE diem;` rồi mở danh sách | Hiện "Chưa có điểm nào", không lỗi | |
