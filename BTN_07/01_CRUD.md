# 01. CRUD – Kết quả thảo luận

## 1. Khái niệm

**CRUD** là bốn thao tác cơ bản khi làm việc với dữ liệu:

| Thao tác | Ý nghĩa | SQL | HTTP |
|---|---|---|---|
| **C**reate | Tạo mới bản ghi | `INSERT` | `POST` |
| **R**ead | Đọc, truy vấn dữ liệu | `SELECT` | `GET` |
| **U**pdate | Cập nhật bản ghi | `UPDATE` | `PUT` / `PATCH` |
| **D**elete | Xóa bản ghi | `DELETE` | `DELETE` |

CRUD không phải một công nghệ cụ thể mà là một **mô hình chức năng**. Hầu hết ứng dụng quản lý (sinh viên, sản phẩm, đơn hàng...) đều xoay quanh bốn thao tác này.

## 2. Ưu điểm và hạn chế

**Ưu điểm**
- Đơn giản, dễ hiểu, dễ chuẩn hóa.
- Phù hợp để học và làm nền tảng cho ứng dụng lớn hơn.

**Hạn chế**
- Chỉ mô tả thao tác dữ liệu, chưa thể hiện nghiệp vụ phức tạp (ví dụ "khóa điểm" không chỉ là một lần cập nhật).
- Xóa cứng (`DELETE`) làm mất dữ liệu vĩnh viễn, nên thực tế thường dùng **xóa mềm**.

## 3. Áp dụng vào bài Quản lý Điểm

Bài cài đặt CRUD cho ba đối tượng, kèm thùng rác để khôi phục.

| Đối tượng (tệp) | Create | Read | Update | Delete |
|---|---|---|---|---|
| **Điểm** (`index.php`) | `INSERT INTO diem` | `SELECT … JOIN` kèm bộ lọc | `UPDATE` điểm QT/thi, tự tính tổng kết + xếp loại | Xóa mềm: `SET deleted_at = NOW()` |
| **Sinh viên** (`student.php`) | `INSERT INTO sinhvien` | Danh sách sinh viên `JOIN lop` | `UPDATE` thông tin sinh viên | Xóa mềm |
| **Môn học** (`subject.php`) | `INSERT INTO hocphan` | Danh sách môn học | `UPDATE` tên, số tín chỉ | Xóa mềm |
| **Thùng rác** (`trash.php`) | | `SELECT … WHERE deleted_at IS NOT NULL` | | **Restore**: `SET deleted_at = NULL` |

### 3.1. Xóa mềm (soft delete)

Thay vì `DELETE`, bài đặt cột `deleted_at`:

```sql
UPDATE diem
   SET deleted_at = NOW()
 WHERE id = :id
   AND trang_thai <> 'Da_khoa'
   AND deleted_at IS NULL;
```

Mọi truy vấn xem dữ liệu đều có điều kiện `deleted_at IS NULL`. Nhờ vậy có thể khôi phục dữ liệu trong trang Thùng rác.

### 3.2. Nghiệp vụ vượt ra ngoài CRUD cơ bản

- **Điểm tổng kết** = `0.4 × điểm quá trình + 0.6 × điểm thi` (làm tròn 2 chữ số).
- **Xếp loại**: A (≥ 8.5), B (≥ 7.0), C (≥ 5.5), D (≥ 4.0), F (< 4.0).
- **Vòng đời trạng thái** của điểm: `Chưa nhập → Đã nhập → Đã khóa`.
  - Điểm đã khóa **không được sửa, không được xóa**. Điều kiện này được chặn ngay trong câu SQL (`WHERE trang_thai <> 'Da_khoa'`), không chỉ ẩn nút trên giao diện.
- **Read nâng cao**: tìm theo mã/họ tên (`LIKE … ESCAPE`), lọc theo học phần, xếp loại, trạng thái, học kỳ, năm học; thống kê số lượng từng xếp loại và điểm trung bình.

### 3.3. Ràng buộc dữ liệu bảo vệ CRUD

| Ràng buộc | Tác dụng |
|---|---|
| `UNIQUE (sinhvien_id, hocphan_id, hoc_ky, nam_hoc)` | Không tạo trùng bản ghi điểm |
| `FOREIGN KEY … ON DELETE RESTRICT` | Không xóa cứng dữ liệu đang được tham chiếu |
| `CHECK (diem_qt BETWEEN 0 AND 10)` | Chặn điểm ngoài khoảng hợp lệ |
| `CHECK (trang_thai IN (...))` | Chỉ cho phép 3 trạng thái hợp lệ |

### 3.4. Kiểm tra đầu vào

Hàm `parse_diem()` chấp nhận dấu `,` hoặc `.`, từ chối chữ, số âm, số lớn hơn 10, quá 2 chữ số thập phân. Hàm `doc_id()` chỉ nhận ID nguyên dương. Mọi câu lệnh SQL dùng **prepared statement** để chống SQL Injection.

## 4. Kết luận thảo luận

1. Bài đã cài đặt đủ Create – Read – Update – Delete cho ba đối tượng.
2. Delete được thiết kế là **xóa mềm**, kéo theo thao tác bổ sung là **Restore**.
3. Điểm khác biệt của bài so với CRUD thuần là các quy tắc nghiệp vụ (tính điểm, xếp loại, khóa điểm), được đảm bảo bằng cả mã PHP lẫn ràng buộc CSDL.
