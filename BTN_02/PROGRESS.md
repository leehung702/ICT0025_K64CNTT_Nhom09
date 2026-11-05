# Họ tên thành viên
- Lê Tiến Hùng
- Lò Thị Huệ
- Sụ li Ta Phim Ma Vông
- Bua Văn Văn Lo Khăm
- Sổm Mai Đao Thạ Kun

# 1. Phân công
- Nhiệm vụ 1 + 2: Lê Tiến Hùng, Sụ li Ta Phim Ma Vông
- Nhiệm vụ 3 + 4: Lò Thị Huệ, Bua Văn Văn Lo Khăm, Sổm Mai Đao Thạ Kun

# Kết quả thảo luận

# 2. Nhiệm vụ 1

## 2.1. Điều kiện

**Dự đoán ví dụ A**
- Điểm = 5 → KQ: Cần cố gắng
- Điểm = 6.5 → KQ: Khá
- Điểm = 8 → KQ: Giỏi

**Chạy thử**
- KQ như dự đoán

**Giải thích**
- Mỗi mức điểm thuộc một điều kiện khác nhau nên cho kết quả khác nhau.

**Dự đoán và thực tế ví dụ B**
- Trạng thái 1 vào case 1
- Trạng thái 2 vào case 2
- Trạng thái 9 vào case default

**Giải thích**
- Default dùng để xử lý các trường hợp không khớp với case nào.
- Khi bỏ break sau case 2 thì chương trình tiếp tục chạy sang case tiếp theo.

## 2.2. Vòng lặp

**Dự đoán và thực tế ví dụ C**
- In ra: 1, 2, 3, 4, 5
- Số lần lặp: 5 lần
- Sau khi thoát vòng lặp i = 6

**Giải thích**
- Điều kiện i <= 5 nên chương trình chạy từ 1 đến 5.
- Sau khi in số 5 thì i tăng lên 6 nên dừng.
- Nếu đổi thành i < 5 thì chỉ in từ 1 đến 4.

**Dự đoán và thực tế ví dụ D**
- In ra: 5, 4, 3, 2, 1
- Số lần lặp: 5 lần
- Sau khi kết thúc n = 0

**Giải thích**
- Mỗi lần lặp n giảm 1.
- Khi n = 0 thì điều kiện không còn đúng nên dừng.
- Nếu đổi n-- thành n++ thì chương trình lặp vô hạn.

# 3. Nhiệm vụ 2

## Kết quả chạy thử

**If Else**

- Trạng thái = 1 → Mới tạo
- Trạng thái = 2 → Đang xử lý
- Trạng thái = 9 → Không xác định

**Switch Case**

- Trạng thái = 1 → Mới tạo
- Trạng thái = 2 → Đang xử lý
- Trạng thái = 9 → Không xác định

**Nhận xét**
- Hai cách cho cùng kết quả.
- Break giúp chương trình dừng đúng tại case tương ứng.

# 4. Nhiệm vụ 3

## Kết quả chạy thử

### For

- SoLuong = 0 → Không in gì
- SoLuong = 1 → Công việc 1
- SoLuong = 5 → Công việc 1 đến Công việc 5

### While

- SoLuong = 0 → Không in gì
- SoLuong = 1 → Công việc 1
- SoLuong = 5 → Công việc 1 đến Công việc 5

**Nhận xét**
- Hai vòng lặp cho kết quả giống nhau.
- Điều kiện dừng hoạt động đúng.

# 5. Nhiệm vụ 4

## Tích hợp vào Project

- Nhãn trạng thái sử dụng switch.
- Danh sách công việc sử dụng vòng lặp for.

## Kiểm thử

- Trạng thái = 1, SoLuong = 0 → Đạt
- Trạng thái = 1, SoLuong = 1 → Đạt
- Trạng thái = 2, SoLuong = 5 → Đạt
- Trạng thái = 9, SoLuong = 0 → Đạt
- Trạng thái = 9, SoLuong = 1 → Đạt
- Trạng thái = 9, SoLuong = 5 → Đạt



