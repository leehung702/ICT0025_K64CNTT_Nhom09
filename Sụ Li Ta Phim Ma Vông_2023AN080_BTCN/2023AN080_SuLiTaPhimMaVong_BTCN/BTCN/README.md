# BTCN - Quản lý điểm học phần (bảng `diem`)

- Họ tên: Sụ Li Ta Phim Ma Vông   MSSV: 2023AN080   Nhóm: 09
- Đề tài của nhóm: Quản lý lớp, sinh viên, học phần và điểm (bộ khung 4 bảng `lop`, `sinhvien`, `hocphan`, `diem`).
- Phần việc nhóm phân công cho em: **Thành viên 4** - bảng `diem`, luồng nhập điểm và tính điểm tổng kết (CRUD độc lập, không dùng chung mã với Thành viên 5).

## Chức năng
Xem / Thêm / Sửa / Xóa điểm học phần; tự tính điểm tổng kết và xếp loại A-F; trạng thái `Chua_nhap`, `Da_nhap`, `Da_khoa`.

## Cách chạy (localhost)
1. Bật Apache và MySQL trong XAMPP. Chép thư mục này vào `htdocs` (ví dụ `htdocs/BTCN`).
2. Tạo CSDL: phpMyAdmin -> Import -> chọn `database.sql` (CSDL trống). Tệp tạo CSDL `qldiem`, 4 bảng, khóa ngoại, ràng buộc và dữ liệu mẫu.
3. Cấu hình: sao chép `config.example.php` thành `config.php`, sửa user/mật khẩu nếu máy khác mặc định (không nộp `config.php`).
4. Mở `http://localhost/BTCN/index.php`.

## Quy tắc nghiệp vụ
- Điểm tổng kết = điểm quá trình x 40% + điểm thi x 60%, làm tròn 1 chữ số thập phân.
- Xếp loại: A >= 8.5; B >= 7.0; C >= 5.5; D >= 4.0; F < 4.0.
- Mỗi ô điểm là số từ 0 đến 10 (chấp nhận `7,5`). Trống cả hai ô -> `Chua_nhap`; chỉ nhập một ô -> báo lỗi.
- Mỗi cặp (sinh viên, học phần) chỉ có một bản ghi. Điểm `Da_khoa` không sửa, không xóa được.

## Các tệp
| Tệp | Vai trò |
|---|---|
| `index.php` | Xem danh sách (JOIN 3 bảng); nút Sửa / Khóa / Xóa |
| `them.php` | Thêm: form, kiểm tra dữ liệu, INSERT (prepared statement) |
| `sua.php` | Sửa điểm của một bản ghi theo `id` |
| `xoa.php` | Xóa theo `id`, chỉ nhận POST |
| `khoa.php` | Chuyển `Da_nhap` -> `Da_khoa` (POST) |
| `db.php`, `helpers.php` | Kết nối CSDL; hàm tính điểm, xếp loại, kiểm tra điểm, `h()` = htmlspecialchars |
| `database.sql` | 4 bảng, khóa ngoại, CHECK, dữ liệu mẫu (không có lệnh xóa) |
| `config.example.php` | Mẫu cấu hình, không có mật khẩu thật |

## Nhật ký kiểm thử
Hai cột cuối ghi kết quả quan sát được. Nếu còn lỗi, ghi rõ thông báo lỗi và những cách đã thử.
- Lần chạy 1: công cụ AI (Claude) chạy tự động đủ 17 ca với PHP 8.3 và MariaDB 10.11, sau khi import `database.sql` vào CSDL trống. Kết quả ở bảng dưới.
- Lần chạy 2 trên XAMPP của em: ........ (ghi hệ điều hành, phiên bản XAMPP/PHP/MySQL; chạy lại các ca và sửa cột "Kết quả thực tế" nếu khác).

| # | Nội dung thử | Dữ liệu nhập | Kết quả mong đợi | Kết quả thực tế | Đạt? |
|---|---|---|---|---|---|
| 1 | Danh sách có dữ liệu | Mở `index.php` sau khi import | Hiện bảng với các dòng mẫu, có tên SV và học phần | Hiện bảng 5 dòng mẫu, có tên sinh viên và học phần | Đạt |
| 2 | Danh sách rỗng | Xóa hết bản ghi `diem` rồi mở `index.php` | Hiện "Chưa có bản ghi điểm nào." | Hiện "Chưa có bản ghi điểm nào." | Đạt |
| 3 | Thêm đúng | SV, HP chưa có điểm; QT=8, Thi=9 | Lưu; TK=8.6, xếp loại A, `Da_nhap` | Lưu; TK=8.60, A, `Da_nhap` | Đạt |
| 4 | Thêm đúng, dấu phẩy | QT=7,5; Thi=6,5 | TK=6.9, xếp loại C | Lưu; TK=6.90, C, `Da_nhap` | Đạt |
| 5 | Thêm trống cả hai ô | QT, Thi để trống | Lưu `Chua_nhap`, điểm trống | Lưu `Chua_nhap`; điểm, TK, xếp loại đều NULL | Đạt |
| 6 | Nhập thiếu một ô | QT=5, Thi trống | Báo phải nhập đủ hai điểm, không lưu | Báo "Hãy nhập đủ cả hai điểm..."; số dòng không đổi | Đạt |
| 7 | Chưa chọn SV/HP | Không chọn | Báo chọn sinh viên, học phần | Báo "Hãy chọn sinh viên." và "Hãy chọn học phần." | Đạt |
| 8 | Nhập sai: điểm âm | QT=-1, Thi=5 | Báo "không được âm", không lưu | Báo "Điểm quá trình không được âm (bạn nhập: -1)"; không lưu | Đạt |
| 9 | Nhập sai: điểm > 10 | QT=5, Thi=11 | Báo "không được lớn hơn 10", không lưu | Báo "Điểm thi không được lớn hơn 10 (bạn nhập: 11)"; không lưu | Đạt |
| 10 | Nhập sai: không phải số | QT=abc, Thi=5 | Báo "phải là một số", không lưu | Báo "Điểm quá trình phải là một số (bạn nhập: abc)"; không lưu | Đạt |
| 11 | Nhập sai: trùng cặp | Cặp SV-HP đã có điểm | Báo trùng, gợi ý dùng Sửa | Báo "Sinh viên này đã có điểm học phần này..."; không lưu | Đạt |
| 12 | Sửa một bản ghi | Bản ghi chưa nhập -> QT=10, Thi=10 | TK=10.0, A, `Da_nhap` | Chuyển về danh sách; TK=10.00, A, `Da_nhap` | Đạt |
| 13 | Sửa sai | Sửa QT=-2 | Báo lỗi, dữ liệu cũ giữ nguyên | Báo "không được âm (bạn nhập: -2)"; dữ liệu cũ giữ nguyên (10, 10, A) | Đạt |
| 14 | Xóa một bản ghi | Bấm Xóa, xác nhận | Bản ghi biến mất, các dòng khác còn | Chuyển về danh sách; bản ghi bị xóa, các dòng khác còn | Đạt |
| 15 | Điểm đã khóa | Mở Sửa / bấm Xóa bản ghi `Da_khoa` | Không sửa, không xóa được | Sửa trả 403; xóa báo "không xóa được"; bản ghi còn nguyên | Đạt |
| 16 | Xóa bằng link GET | Mở `xoa.php?id=1` trên trình duyệt | Bị từ chối (405), không xóa | Trả 405; bản ghi còn nguyên | Đạt |
| 17 | Chống XSS | QT=`<b>x</b>` | Hiện nguyên chữ, không in đậm | Hiện nguyên chữ `&lt;b&gt;x&lt;/b&gt;` trong thông báo, không in đậm | Đạt |
