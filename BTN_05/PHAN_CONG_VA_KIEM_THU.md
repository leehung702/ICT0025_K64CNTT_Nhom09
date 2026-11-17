# Phân công, kiểm thử và thuyết trình — Nhóm 09

## Thành viên và phân công đề xuất

| Thành viên | Vai trò chính | Phần trình bày |
|---|---|---|
| Lê Tiến Hùng | Điều phối; kiểm tra import `database.sql` và kết nối | CSDL, sơ đồ 4 bảng và khóa ngoại |
| Lò Thị Huệ | Người viết form và kiểm tra trường bắt buộc | HTML form, dữ liệu `POST`, validation |
| Sụ li Ta Phim Ma Vông | Người viết lệnh thêm dữ liệu | PDO `prepare`, tham số và `execute` |
| Bua Văn Văn Lo Khăm | Người viết truy vấn danh sách | `SELECT`, `foreach`, xếp loại điểm |
| Sổm Mai Đao Thạ Kun | Người kiểm thử và rà soát đầu ra | `htmlspecialchars`, ba ca kiểm thử, tổng kết |

**Đổi người viết và người kiểm tra:**
- Lò Thị Huệ kiểm tra phần form của Sụ li Ta Phim Ma Vông; 
- Sụ li Ta Phim Ma Vông kiểm tra phần form của Lò Thị Huệ. 
- Bua Văn Văn Lo Khăm kiểm tra kết quả truy vấn của Sổm Mai Đao Thạ Kun; 
- Sổm Mai Đao Thạ Kun rà soát truy vấn của Bua Văn Văn Lo Khăm. 
- Lê Tiến Hùng kiểm tra lại cấu hình kết nối và bản nộp chung. 

## Thiết kế dữ liệu

- `lop(ma_lop, ten_lop)`: danh sách lớp.
- `sinhvien(ma_sv, ho_ten, ma_lop)`: sinh viên thuộc một lớp.
- `hocphan(ma_hp, ten_hp, so_tin_chi)`: danh mục học phần.
- `diem(id, ma_sv, ma_hp, diem_so, ngay_tao)`: kết quả; khóa ngoại tới sinh viên/học phần, điểm từ 0 đến 10. Ràng buộc `UNIQUE(ma_sv, ma_hp)` ngăn nhập trùng trong bài tập.

## Kiểm thử 3 trường hợp bắt buộc

| Ca | Đầu vào/thao tác | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|
| 1. Nhập hợp lệ | Chọn Nguyễn Minh Anh, học phần Thiết kế Web, điểm `8.5`; bấm Lưu | Thông báo lưu thành công; danh sách có dòng tương ứng; xếp loại Giỏi | Ghi sau khi chạy: Đúng kết quả dự đoán |
| 2. Thiếu dữ liệu | Không chọn sinh viên (hoặc để trống trường điểm), gửi form | Báo trường bắt buộc; không có bản ghi mới trong `diem` | Ghi sau khi chạy: Đúng kết quả dự đoán |
| 3. Danh sách rỗng | Trước khi thêm điểm, chạy `DELETE FROM diem;` trong CSDL | Hiện “Chưa có điểm nào”; không lỗi trang | Ghi sau khi chạy: Đúng kết quả dự đoán |

Có thể kiểm tra thêm điểm `-1`, `10.1`, chữ bất kỳ; điểm không hợp lệ phải bị từ chối. Không chọn cùng sinh viên + học phần đã lưu hai lần vì ràng buộc duy nhất.

## Luồng cần giải thích

1. Trình duyệt gửi form bằng `POST` đến `index.php`.
2. PHP đọc `$_POST`, kiểm tra các trường bắt buộc, kiểm tra điểm là số từ 0 đến 10 và xác minh sinh viên/học phần có tồn tại.
3. Nếu hợp lệ, PDO tạo `INSERT INTO diem ... VALUES (:ma_sv, :ma_hp, :diem_so)` bằng `prepare`; `execute` truyền giá trị riêng qua tham số. Nếu thiếu/sai thì không gọi INSERT.
4. PHP chạy `SELECT` kết hợp các bảng, sau đó `foreach` duyệt kết quả để tạo hàng HTML. Xếp loại được tính từ điểm.
5. Mọi giá trị dữ liệu đưa vào HTML được bọc bởi `htmlspecialchars` (hàm `e`) để tránh nội dung nhập vào được diễn giải thành HTML/script.
