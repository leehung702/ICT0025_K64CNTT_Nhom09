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


---

# BTN 06 — Sửa, xóa mềm, kiểm thử

## Phân công đề xuất (đổi vai ở Tiết 2)

| Thành viên | Việc làm | Người kiểm tra chéo |
|---|---|---|
| Lê Tiến Hùng | `database.sql`/migration, cột `deleted_at`, ráp bản nộp | Sổm Mai Đao Thạ Kun |
| Lò Thị Huệ | Form sửa (`edit.php`), trường bắt buộc | Sụ li Ta Phim Ma Vông |
| Sụ li Ta Phim Ma Vông | `UPDATE ... WHERE id = :id` + prepared statement | Lò Thị Huệ |
| Bua Văn Văn Lo Khăm | Xóa mềm, xác nhận xóa (POST), thùng rác/khôi phục | Sổm Mai Đao Thạ Kun |
| Sổm Mai Đao Thạ Kun | Kiểm thử, ghi lỗi, báo cáo | Bua Văn Văn Lo Khăm |

## Hậu quả nếu thiếu điều kiện WHERE

- `UPDATE diem SET diem_so = 9;` (không WHERE) → **mọi** dòng điểm bị đổi thành 9.
- `DELETE FROM diem;` (không WHERE) → **toàn bộ** bảng điểm bị xóa, không hoàn tác được nếu không có sao lưu.
- Vì vậy mọi lệnh đều dùng `WHERE id = :id`; xóa mềm giúp khôi phục khi xóa nhầm.

## Kịch bản kiểm thử BTN 06 (ghi kết quả thực tế sau khi chạy)

| Ca | Thao tác | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|
| 1. Thêm, xem | Thêm điểm 8.5 cho Nguyễn Minh Anh – Thiết kế Web | Có dòng mới, xếp loại Giỏi | Đúng dự đoán |
| 2. Sửa đúng dòng | Bấm Sửa dòng đó, đổi điểm thành 6.5, Cập nhật | Báo cập nhật thành công; xếp loại Trung bình; các dòng khác không đổi | Đúng dự đoán |
| 3. Sửa, dữ liệu sai | Ở form sửa nhập `11`, `-1`, `abc`, để trống | Báo lỗi, dữ liệu trong CSDL không đổi | Đúng dự đoán |
| 4. Sửa, không thay đổi | Bấm Cập nhật mà không đổi gì | Báo "Chưa có thay đổi nào" | Đúng dự đoán |
| 5. Sửa, ID không tồn tại | Mở `edit.php?id=9999` và `edit.php?id=abc` | Trang báo không tìm thấy/không hợp lệ, không lỗi PHP | Đúng dự đoán |
| 6. Hủy xóa | Bấm Xóa rồi chọn Hủy | Không có yêu cầu gửi đi; dòng vẫn còn | Đúng dự đoán |
| 7. Xóa đã xác nhận | Bấm Xóa rồi OK | Dòng biến mất khỏi danh sách, xuất hiện ở Thùng rác; các dòng khác còn nguyên | Đúng dự đoán |
| 8. Xóa ID không tồn tại | Dùng công cụ dev sửa `id` trong form xóa thành 9999, gửi | Báo "Không tìm thấy điểm cần xóa" | Đúng dự đoán |
| 9. Khôi phục | Ở Thùng rác bấm Khôi phục | Dòng quay lại danh sách | Đúng dự đoán |
| 10. Thêm lại cặp đã xóa mềm | Xóa mềm một dòng, rồi thêm lại đúng sinh viên + học phần đó | Lưu thành công, không báo trùng, danh sách chỉ có một dòng cho cặp đó | Đúng dự đoán |

## Phần còn thiếu / hạn chế đã biết

- Chưa có chống CSRF và chưa có đăng nhập (ngoài phạm vi bài).
- Chưa có xóa vĩnh viễn từ thùng rác (chủ ý, để tránh mất dữ liệu).

## Kiểm thử thêm: nhập tên mới (ghi kết quả thực tế sau khi chạy)

| Ca | Thao tác | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|
| 11. Sinh viên mới | Gõ tên chưa có, chọn lớp, chọn/nhập học phần có sẵn, điểm 7 | Lưu thành công; sinh viên mới xuất hiện trong danh sách gợi ý | Đúng dự đoán |
| 12. Tên chưa có, không chọn lớp | Gõ tên lạ, để trống lớp | Báo lỗi, không tạo sinh viên | Đúng dự đoán |
| 13. Học phần mới | Gõ tên học phần lạ, mã `WEB102`, 3 tín chỉ | Lưu thành công; học phần mới có trong gợi ý | Đúng dự đoán |
| 14. Học phần mới thiếu mã / mã trùng | Để trống mã, hoặc dùng mã `WEB101` | Báo lỗi, không có sinh viên/học phần nào được tạo (hoàn tác) | Đúng dự đoán |
| 15. Tên có sẵn | Chọn đúng tên trong gợi ý | Dùng sinh viên/học phần cũ, không tạo bản ghi mới | Đúng dự đoán |
