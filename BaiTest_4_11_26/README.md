## Thông tin sinh viên
- Họ tên: Lê Tiến Hùng
- Mã sinh viên: 2023A0552

## Câu trả lời 

### Ví dụ A:
**Trả lời**
- Dự đoán trước khi chạy: Khá
- KQ sau khi chạy: Khá

- Với $diem = 5 KQ cho ra là "Cần cố gắng hơn!"
- Với $diem = 6.5 KQ cho ra là "Khá"
- Với $diem = 8 KQ cho ra là "Giỏi"

**Giải thích**
- 6.5 in ra "Khá" mà không phải "Giỏi" vì cấu trúc if...elseif...else kiểm tra điều kiện tuần tự từ trên xuống dưới và sẽ dừng lại ở nhánh đầu tiên có điều kiện đúng.


### Ví dụ B — switch:
**Trả lời**
- Dự đoán KQ ban đầu ($trangThai = 2): Đang xử lý
- KQ sau khi chạy: Đang xử lý

- Khi xóa lệnh `break` ở `case 1` và đặt `$trangThai = 1`: KQ in ra là "Mới tạoĐang xử lý".
- Thử đặt `$trangThai = 9`: KQ in ra là "Không xác định".

**Giải thích**
- Khi xóa `break` ở `case 1`, PHP gặp hiện tượng trôi lệnh (fall-through). Nó in ra "Mới tạo" rồi tiếp tục chạy tuột xuống các dòng mã của `case 2` bên dưới và in thêm "Đang xử lý" cho đến khi gặp lệnh `break` của case 2.
- Giá trị 9 không khớp với `case 1` hay `case 2`, do đó chương trình tự động rẽ vào nhánh `default`.


### Ví dụ C — for:
**Trả lời**
- Với `$i <= 5`: 
  - Dự đoán: Lặp 5 lần (in ra 1 2 3 4 5). Giá trị cuối của `$i` là 6.
  - KQ sau khi chạy: Đúng như dự đoán.
- Với `$i < 5`: 
  - Dự đoán: Lặp 4 lần (in ra 1 2 3 4). Giá trị cuối của `$i` là 5.
  - KQ sau khi chạy: Đúng như dự đoán.

**Giải thích**
- Điều kiện `$i <= 5` cho phép vòng lặp thực thi cả khi `$i` bằng 5. Khi `$i` tăng lên 6, điều kiện mới sai và dừng lặp.
- Ở điều kiện `$i < 5`, vòng lặp kiểm tra nghiêm ngặt không lấy dấu bằng, nên khi `$i` tăng lên tới 5 là vòng lặp đã lập tức kết thúc.


### Ví dụ D — while:
**Trả lời**
- Với `$n = $n - 3`: 
  - Dự đoán: Lặp 4 lần (in ra 10 7 4 1). Giá trị cuối cùng của `$n` là -2.
  - KQ sau khi chạy: Đúng như dự đoán.
- Với `$n = $n + 3`: 
  - Dự đoán: Vòng lặp chạy vô hạn không dừng lại.
  - KQ sau khi chạy: Vòng lặp vô tận, chương trình bị treo (cần nhấn Ctrl+C để dừng).

**Giải thích**
- Khi đổi thành `$n = $n + 3`, giá trị của biến `$n` sẽ liên tục tăng (13, 16, 19...). Do đó, biểu thức điều kiện `$n > 0` luôn luôn mang giá trị `true`. Vì điều kiện không bao giờ trở thành `false` nên vòng lặp không thể kết thúc.