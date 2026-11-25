Bua VĂn VĂn Lo Khăm_2023AN048
# BÁO CÁO CÁ NHÂN – THÀNH VIÊN 3

## 1. Phần việc được giao
- Thiết kế và mô tả bảng `hocphan`, `diem`.
- Xây dựng tệp SQL chung của nhóm sau khi thống nhất cấu trúc với các thành viên.
- Xây dựng CRUD cá nhân cho bảng `hocphan`.

## 2. Chức năng CRUD đã thực hiện
- Xem danh sách và tìm kiếm học phần.
- Thêm học phần.
- Cập nhật học phần.
- Xóa học phần bằng phương thức POST.

## 3. Kiểm tra dữ liệu
- Mã học phần không được để trống và không được trùng.
- Tên học phần không được để trống.
- Số tín chỉ phải là số nguyên dương.
- Các truy vấn sử dụng PDO Prepared Statement.
- Dữ liệu hiển thị được xử lý bằng `htmlspecialchars()`.

## 4. Lỗi đã gặp và cách khắc phục
| Lỗi | Cách khắc phục |
|---|---|
| Chưa kết nối được MySQL | Kiểm tra MySQL trong XAMPP và thông tin cấu hình |
| Import SQL lỗi | Kiểm tra tên CSDL, cú pháp SQL và thứ tự tạo bảng |
| Mã học phần bị trùng | Kiểm tra trước khi thêm/cập nhật và đặt UNIQUE trong CSDL |

## 5. Nhật ký sử dụng AI
Ghi rõ các câu hỏi đã đặt ra, nội dung AI hỗ trợ, phần mã đã tự kiểm tra/chỉnh sửa và kết quả chạy thử. Không ghi rằng đã tự viết toàn bộ nếu có sử dụng công cụ hỗ trợ.
