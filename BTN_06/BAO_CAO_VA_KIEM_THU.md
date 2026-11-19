# Báo cáo thực hành BTN 06 — Nhóm 09

**Đề tài:** Quản lý sinh viên và điểm học phần  
**Nội dung:** `UPDATE`, `DELETE`, `WHERE`, prepared statement, xác nhận xóa, xóa mềm (Soft Delete).

## 1. Mục tiêu và thiết kế

Nhóm tiếp tục quản lý đối tượng **điểm học phần** ở bảng `diem`. Khóa chính `id` xác định một dòng điểm duy nhất. Form sửa nạp dữ liệu hiện tại theo ID và cho phép cập nhật sinh viên, học phần, điểm. Danh sách có nút **Sửa** và **Xóa** cho từng dòng.

Câu lệnh sửa (có điều kiện khóa chính):

```sql
UPDATE diem
SET ma_sv = :ma_sv, ma_hp = :ma_hp, diem_so = :diem_so
WHERE id = :id AND deleted_at IS NULL
```

Câu lệnh xóa mềm (có điều kiện khóa chính) và khôi phục:

```sql
UPDATE diem SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id AND deleted_at IS NULL;
UPDATE diem SET deleted_at = NULL              WHERE id = :id AND deleted_at IS NOT NULL;
```

Các lệnh đều chạy bằng PDO `prepare()` và `execute()`; ID không bao giờ được ghép trực tiếp vào chuỗi SQL. Xóa chỉ nhận yêu cầu POST từ nút trên trang; trình duyệt hỏi xác nhận trước khi gửi, bấm Hủy thì không gửi gì nên dữ liệu giữ nguyên.

**Vì sao xóa mềm:** dòng bị xóa chỉ được đánh dấu bằng cột `deleted_at`, vẫn còn trong CSDL và khôi phục được khi xóa nhầm. Xóa cứng (`DELETE`) làm mất hẳn dòng; nếu thiếu `WHERE`, `UPDATE` đổi mọi dòng và `DELETE` xóa cả bảng.

## 2. Phân công nhóm 5 thành viên (đề xuất, đổi vai ở Tiết 2)

| Thành viên | Phần thực hiện | Người kiểm tra / phần trình bày |
|---|---|---|
| Lê Tiến Hùng | Điều phối, CSDL, cột `deleted_at`, khóa chính, kết nối | Rà soát `WHERE id`; trình bày cấu trúc dữ liệu |
| Lò Thị Huệ | Form sửa (`edit.php`), nạp dữ liệu hiện tại theo ID | Sụ li Ta Phim Ma Vông kiểm tra validation form |
| Sụ li Ta Phim Ma Vông | Xử lý `UPDATE` bằng prepared statement | Lò Thị Huệ kiểm tra ID và kết quả cập nhật |
| Bua Văn Văn Lo Khăm | Nút xóa, hộp xác nhận, xóa mềm, thùng rác và khôi phục | Sổm Mai Đao Thạ Kun kiểm thử hủy/xác nhận xóa |
| Sổm Mai Đao Thạ Kun | Chạy kịch bản kiểm thử, ghi lỗi, hoàn thiện báo cáo | Bua Văn Văn Lo Khăm kiểm tra danh sách sau thao tác |

Các cặp người viết/kiểm tra được phân chéo để không ai tự kiểm tra phần mình; Lê Tiến Hùng tổng kiểm tra bản nộp và điều phối phần trình bày.

## 3. Kịch bản kiểm thử

Điền cột "Kết quả thực tế" sau khi chạy trên máy nhóm; không ghi đạt trước khi kiểm tra.

| Ca | Thao tác / đầu vào | Kết quả mong đợi | Kết quả thực tế |
|---|---|---|---|
| 1. Thêm, xem | Thêm điểm 8.5 cho Nguyễn Minh Anh – Thiết kế Web | Có dòng mới, xếp loại Giỏi |Giỏi |
| 2. Sửa hợp lệ | Bấm Sửa, đổi điểm thành `7.5`, lưu | Báo thành công; đúng dòng đổi, các dòng khác giữ nguyên | Đúng dự đoán |
| 3. Sửa ID không tồn tại | Mở `edit.php?id=999999` và `edit.php?id=abc` | Báo không tìm thấy / không hợp lệ, không lỗi PHP | Đúng dự đoán |
| 4. Dữ liệu sửa sai | Để trống tên/điểm, nhập `11`, `-1`, `abc` | Báo lỗi; dữ liệu cũ nguyên vẹn | Đúng dự đoán |
| 5. Sửa không thay đổi | Bấm Lưu mà không đổi gì | Báo "Chưa có thay đổi nào" | Đúng dự đoán |
| 6. Hủy xóa | Bấm Xóa rồi chọn Cancel | Không gửi yêu cầu; dòng vẫn còn | Đúng dự đoán |
| 7. Xóa xác nhận | Bấm Xóa rồi OK | Dòng sang tab Thùng rác; các dòng khác nguyên vẹn | Đúng dự đoán |
| 8. ID xóa không tồn tại | Dùng công cụ dev đổi `id` trong form xóa thành 9999 | Báo "Không tìm thấy", không ảnh hưởng dòng khác | Đúng dự đoán |
| 9. Khôi phục | Ở Thùng rác bấm Khôi phục | Dòng quay lại danh sách chính | Đúng dự đoán |
| 10. Thêm lại cặp đã xóa mềm | Xóa mềm một dòng rồi thêm lại đúng sinh viên + học phần | Lưu thành công, chỉ còn một dòng cho cặp đó | Đúng dự đoán |
| 11. Sinh viên mới | Form "Thêm sinh viên mới", chọn lớp | Báo thành công; tên xuất hiện trong gợi ý | Đúng dự đoán |
| 12. Học phần mới | Mã `NET201`, tên và tín chỉ hợp lệ; thử lại với mã trùng | Lần đầu thành công; lần hai báo mã đã tồn tại | Đúng dự đoán |
| 13. Danh sách rỗng | Xóa mềm hết các dòng | Hiện "Chưa có điểm nào", không lỗi trang | Đúng dự đoán |
| 14. Refresh sau thao tác | Tải lại trang sau khi thêm/sửa/xóa thành công | Không gửi lại POST, không tạo/xóa lặp | Đúng dự đoán |

### Cách xác minh trong phpMyAdmin

Chọn CSDL `quan_ly_sinh_vien` → tab SQL:

```sql
SELECT id, ma_sv, ma_hp, diem_so, deleted_at FROM diem ORDER BY id;
```

Sau mỗi thao tác so sánh số dòng, ID và cột `deleted_at`. Không chạy `DELETE FROM diem;` hay `UPDATE` không có `WHERE` trong lúc kiểm thử; thao tác theo ID cụ thể.

## 4. Luồng xử lý và nội dung trình bày

- **Sửa:** chọn dòng → `edit.php?id=ID` đọc đúng bản ghi → form hiện dữ liệu cũ → PHP kiểm tra trường bắt buộc, điểm 0–10, sinh viên/học phần tồn tại, cặp không trùng dòng khác → prepared `UPDATE ... WHERE id = :id` → chuyển hướng về danh sách.
- **Xóa mềm:** bấm Xóa → hộp confirm → Cancel thì dừng ở trình duyệt; OK gửi POST `action=delete` + ID → prepared `UPDATE ... SET deleted_at ... WHERE id = :id` → nếu không có dòng khớp thì báo rõ → chuyển hướng và nạp danh sách mới.
- **Khôi phục:** tab Thùng rác → Khôi phục → `UPDATE ... SET deleted_at = NULL WHERE id = :id`.
- Mọi dữ liệu động hiển thị ra HTML được escape bằng `htmlspecialchars` (hàm `e()`).
- Thiếu `WHERE`: `UPDATE` sửa mọi dòng, `DELETE` xóa toàn bộ bảng — vì vậy khóa chính và tham số `id` là điều kiện bắt buộc.
