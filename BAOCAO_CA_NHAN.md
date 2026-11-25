# BAOCAO_CA_NHAN

Họ tên: Sụ Li Ta Phim Ma Vông   MSSV: 2023AN080   Nhóm: 09

## 1. Phần việc được nhóm phân công
Nhóm phân công em là **Thành viên 4**. Em quản lý dữ liệu **điểm học phần** (bảng `diem`), tập trung vào luồng nhập điểm và tính điểm tổng kết;
em viết CRUD độc lập, không dùng chung mã với Thành viên 5 (cùng bảng `diem`, phụ trách xếp loại và tra cứu).

## 2. Em đã tự viết, sửa và thử những gì
- Em tạo `config.php` từ `config.example.php` trên máy của mình (không nộp tệp này) và điền họ tên, MSSV, nhóm vào README và báo cáo.
- Chỗ em tự sửa trong mã: ........................ (chỉ ghi nếu em thật sự đã sửa, ví dụ đổi trọng số, thêm kiểm tra, đổi tên trường).
- Lỗi đã gặp và cách xử lý:

| Ai phát hiện | Hiện tượng | Nguyên nhân | Cách xử lý |
|---|---|---|---|
| Em tự gặp | Mở `index.php` báo "Chưa có config.php" | Chưa tạo `config.php` | Tạo `config.php` từ `config.example.php` rồi tải lại trang |
| Phát hiện khi AI chạy thử | Nhập -1 hoặc `abc` còn báo thêm "Hãy nhập đủ cả hai điểm" | Tính tổng kết chạy cả khi một ô điểm đã sai | Chỉ tính tổng kết khi từng ô điểm hợp lệ (`them.php`, `sua.php`) |
| Phát hiện khi đối chiếu với nhóm | Công thức 30/70 lệch với Thành viên 5 (40/60) | Chưa thống nhất công thức | Đổi hằng số trong `helpers.php` và tính lại dữ liệu mẫu trong `database.sql` |
| ........ | ........ | ........ | ........ (lỗi khác em gặp khi chạy trên XAMPP, nếu có) |

## 3. Việc dùng AI
- Công cụ: Claude (Anthropic).
- Câu hỏi chính: nhờ làm bài CRUD bảng `diem` theo yêu cầu của nhóm và của thầy (PHP, prepared statement, `htmlspecialchars`, xóa bằng POST), kèm `database.sql`, README và hướng dẫn để em hiểu bài.
- Phần em đã dùng hoặc giữ nguyên: mã CRUD (`index.php`, `them.php`, `sua.php`, `xoa.php`, `khoa.php`), `helpers.php`, `database.sql` và mẫu README do Claude tạo; ........ (em chỉnh lại cho đúng với những tệp em giữ nguyên).
- Phần em đã sửa: ........................
- Cách em kiểm tra: Claude chạy tự động 17 ca (PHP 8.3, MariaDB 10.11) và ghi vào README; em tự chạy trên XAMPP: ........ (ghi các ca em đã thử, ví dụ -1, 11, trùng, sửa, xóa, và kết quả).
