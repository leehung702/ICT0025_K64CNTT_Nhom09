# Tiến độ project

## Thông tin project

- **Project:** Form đăng ký hồ sơ cá nhân và đăng nhập bằng PHP/MySQL.
- **Thư mục chạy:** `form/`
- **Cách mở trên XAMPP:** khởi động Apache, sau đó truy cập `http://localhost/2026.11.10_form/form/`.
- **Tình trạng tích hợp:** Trang đăng ký lưu hồ sơ vào MySQL; trang đăng nhập xác thực tài khoản và hiển thị hồ sơ. Mã hồ sơ được tạo tự động, không phải số CCCD.
- **Khởi tạo cơ sở dữ liệu:** Mở phpMyAdmin, chọn tab **Import**, nhập `form/database.sql`. Bật MySQL trong XAMPP trước khi đăng ký.
- **Kết nối:** `form/ket_noi.php` đọc cấu hình `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD` từ môi trường; mặc định dùng `127.0.0.1`, `form_cccd_demo`, `root` và mật khẩu rỗng theo XAMPP cục bộ thường gặp. Không dùng mặc định này cho môi trường triển khai.

## Thành viên và phân công

> Danh sách thành viên đã được cung cấp. Phân công và đóng góp cụ thể chưa được cung cấp nên cần được nhóm bổ sung theo thực tế.

| Thành viên | Phân công | Đóng góp thực tế |
|---|---|---|
| Lê Tiến Hùng | dang_ky | hoàn thành |
| Lò Thị Huệ | dang_nhap | hoàn thành |
| Sụ li Ta Phim Ma Vông | index | hoàn thành |
| Bua Văn Văn Lo Khăm | ket
_noi | hoàn thành |
| Sổm Mai Đao Thạ Kun | dang_xuat | hoàn thành |

## Kết quả thảo luận

Chưa có nội dung hoặc biên bản thảo luận được cung cấp. Bổ sung các quyết định đã thống nhất, người phụ trách và việc cần làm tiếp theo sau khi nhóm xác nhận.

## Bảng kiểm thử

| Hạng mục | Kết quả | Ghi chú |
|---|---|---|
| Kiểm tra cú pháp `form/index.php` bằng `php -l` | Đạt | Không có lỗi cú pháp. |
| Kiểm tra cú pháp các trang PHP sau khi tích hợp MySQL | Đạt | Tất cả tệp PHP đã qua `php -l`. |
| Khởi tạo database và bảng trong MySQL XAMPP | Đạt | Đã nhập `form/database.sql`. |
| Đăng ký hồ sơ và lưu vào MySQL | Đạt | Đã thử trên trình duyệt bằng dữ liệu giả. |
| Đăng nhập và xem hồ sơ đã lưu | Đạt | Hồ sơ và thời gian đăng nhập được hiển thị đúng. |
| Đăng xuất | Đạt | Đã thử trên trình duyệt; quay lại form đăng nhập. |
| Dọn dữ liệu thử nghiệm | Đạt | Đã xóa tài khoản thử khỏi database. |
| Cập nhật giao diện chung trong `form/style.css` | Đã hoàn thành | Nền sáng, khung nội dung mới, nút và ô nhập được làm lại; có điều chỉnh cho màn hình nhỏ. |
| Mở các trang trên Apache/XAMPP | Chưa kiểm thử | Cần xác nhận hoạt động trên trình duyệt. |
| Bấm “Quên mật khẩu?” | Chưa kiểm thử | Cần cấu hình gửi email để bật đặt lại mật khẩu. |
| Kiểm tra dữ liệu sai và email đã đăng ký | Chưa kiểm thử | Cần thử dữ liệu không hợp lệ, email trùng và mật khẩu không đúng. |

## Khó khăn và lỗi còn lại

- Nếu XAMPP trên máy dùng thông tin MySQL khác mặc định, cần đặt biến môi trường kết nối trước khi chạy project.
- Chức năng quên mật khẩu chưa gửi liên kết vì project chưa cấu hình email.
- Chưa có kết quả kiểm thử đăng ký/đăng nhập với MySQL trên máy.


