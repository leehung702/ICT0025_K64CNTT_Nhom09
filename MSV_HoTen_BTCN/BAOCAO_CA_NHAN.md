# Báo cáo cá nhân - BTCN

- Họ tên: SụLi Ta Phim Ma Vông  Mã sinh viên: 2023AN080
- Nhóm: 09  Vai trò: Thành viên 4 (bảng `diem`, luồng nhập và tính điểm)

> Các mục 3, 4 và 5 phải do bạn tự điền bằng nội dung có thật. Hãy chạy thử, đọc lại mã, và ghi đúng những gì bạn đã làm.

## 1. Phần việc được giao
- Nhóm: mô tả Chức năng 2 (nhập điểm và tính điểm tổng kết), vẽ sơ đồ hoạt động.
- Cá nhân: bài CRUD quản lý điểm học phần (bảng `diem`).

## 2. Tóm tắt giải pháp
- `database.sql`: 4 bảng `lop`, `sinhvien`, `hocphan`, `diem`; khóa ngoại; ràng buộc điểm 0 đến 10; `UNIQUE (sinhvien_id, hocphan_id)`; dữ liệu mẫu.
- Luồng: form (`them.php`, `sua.php`) → PHP kiểm tra dữ liệu → prepared statement → MySQL → `index.php` hiển thị bằng `SELECT` và `foreach`.
- Điểm tổng kết và xếp loại được tính trong PHP (`functions.php`) rồi lưu vào `diem_tk`, `xep_loai`.
- Bảo mật: prepared statement, `htmlspecialchars()`, xóa bằng form POST theo `id`.

## 3. Lỗi đã gặp và cách tự sửa
(Chỉ ghi lỗi bạn thực sự gặp khi chạy. Mỗi dòng nêu: thông báo lỗi, nguyên nhân, cách sửa.)

| # | Lỗi gặp phải | Nguyên nhân | Cách sửa |
|---|--------------|-------------|----------|
| 1 | | | |
| 2 | | | |

## 4. Lịch sử sử dụng AI

| Công cụ | Nội dung nhờ AI | Tôi đã kiểm tra, chỉnh sửa gì |
|---------|-----------------|-------------------------------|
| Claude (Anthropic) | Đọc đề và giáo trình; viết khung mã PHP CRUD bảng `diem`, `database.sql`, mô tả Chức năng 2 và sơ đồ hoạt động | (Điền: phần nào tôi đã đọc hiểu, chạy thử, sửa hoặc viết lại) |

## 5. Tự đánh giá
(Điền: tôi hiểu được phần nào, còn chưa chắc phần nào, và sẽ cải thiện gì.)
