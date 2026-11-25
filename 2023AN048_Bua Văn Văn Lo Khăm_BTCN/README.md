# TV3 – Quản lý học phần

## Công nghệ
- PHP 8+, PDO
- MySQL / MariaDB
- Bootstrap 5.3

## Cài đặt và chạy
1. Giải nén thư mục `TV3_QuanLyHocPhan` vào `C:\xampp\htdocs`.
2. Mở XAMPP Control Panel, bật Apache và MySQL.
3. Mở `http://localhost/phpmyadmin`.
4. Chọn Import, chọn `database/database.sql`, rồi nhấn Import.
5. Nếu tài khoản MySQL khác mặc định, sao chép `config/config.example.php` thành `config/config.php` và sửa thông tin kết nối.
6. Truy cập `http://localhost/TV3_QuanLyHocPhan/`.

## Chức năng
- Xem và tìm kiếm học phần
- Thêm học phần
- Sửa học phần
- Xóa học phần bằng POST

## Kiểm thử gợi ý
- Thêm học phần với mã mới, tên hợp lệ, số tín chỉ 3: phải thành công.
- Bỏ trống mã hoặc tên: phải báo lỗi.
- Nhập số tín chỉ 0 hoặc số âm: phải báo lỗi.
- Nhập mã học phần đã tồn tại: phải báo lỗi.
- Sửa thông tin hợp lệ: dữ liệu được cập nhật.
- Xóa học phần: phải xác nhận trước khi gửi biểu mẫu.
- Tìm kiếm theo mã hoặc tên: chỉ hiện kết quả phù hợp.

## Lưu ý
Đây là SQL cá nhân chỉ tạo bảng `hocphan`. SQL chung của nhóm cần được thống nhất riêng với các thành viên trước khi ghép các bảng `lop`, `sinhvien`, `hocphan`, `diem`.
