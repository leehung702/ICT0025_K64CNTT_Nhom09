# BTN_04 — Quản lý sinh viên và điểm học phần

## Thông tin nhóm và đóng góp

- **Nhóm:** `Nhom09` 
- **Đề tài:** Quản lý sinh viên và điểm học phần.
- **Thành viên, đóng góp thực tế:** 

| Thành viên | Đóng góp thực tế | Tình trạng |
|---|---|---|
| Lê Tiến Hùng | khảo sát yêu cầu, phân công, thiết kế CSDL | hoàn thành |
| Lò Thị Huệ | viết, kiểm thử SQL | hoàn thành |
| Sụ li Ta Phim Ma Vông | phác thảo và rà soát UML USE CASE | hoàn thành |
| Bua Văn Văn Lo Khăm | phác thảo và rà soát UML HOẠT ĐỘNG | hoàn thành |
| Sổm Mai Đao Thạ Kun | viết, kiểm thử dữ liêu giả | hoàn thành |


## Tệp trong thư mục

- `thiet_ke_csdl.pdf`: mô hình quan hệ, thuộc tính, PK/FK và quy tắc nghiệp vụ.
- `01_tao_csdl.sql`: tạo CSDL, 4 bảng `lop`, `sinhvien`, `hocphan`, `diem` và view `v_bang_diem`.
- `02_du_lieu_gia.sql`: dữ liệu hoàn toàn giả lập và truy vấn minh họa (tùy chọn).
- `uml_use_case.png`: tác nhân và các chức năng sơ bộ.
- `uml_hoat_dong.png`: luồng nhập điểm, xác thực, tính và hiển thị kết quả.

## Các giả định cần nhóm xác nhận

1. Mỗi sinh viên thuộc một lớp tại thời điểm hiện tại; chưa lưu lịch sử chuyển lớp.
2. Một bản ghi `diem` ứng với một sinh viên học một học phần ở một học kỳ của một năm học. Có thể học lại ở học kỳ/năm khác. Chưa quản lý đăng ký học phần, phân công giảng viên, tài khoản hoặc phân quyền.
3. Điểm quá trình và điểm thi đều bắt buộc nằm trong khoảng 0–10. Tổng kết = `ROUND(0.40 * điểm quá trình + 0.60 * điểm thi, 2)`.
4. Xếp loại **minh họa**: `>=9` Xuất sắc; `>=8` Giỏi; `>=6.5` Khá; `>=5` Trung bình; thấp hơn là Yếu. Đạt nếu `>=5`. Hãy sửa theo quy định học phần/trường nếu khác.
5. Vai trò trong UML là tác nhân nghiệp vụ; **chưa có đăng nhập/phân quyền**, vì vậy script CSDL chưa thực thi giới hạn ai được nhập hoặc xem điểm. Không công bố dữ liệu thật trên repo công khai.

## Import và kiểm thử

Yêu cầu: **MySQL 8.0.16+** (có thực thi `CHECK`) hoặc **MariaDB 10.5+**, UTF-8. Dòng `CREATE DATABASE` cần quyền tạo CSDL. Nếu môi trường LMS chỉ cho dùng CSDL có sẵn, hãy bỏ hai dòng `CREATE DATABASE` / `USE` và chọn CSDL đó trước khi import.


mysql -u <tai_khoan> -p < 01_tao_csdl.sql
mysql -u <tai_khoan> -p < 02_du_lieu_gia.sql
mysql -u <tai_khoan> -p -D qlsv_diem_hp -e "SELECT ma_sv, ma_hp, diem_tong_ket, xep_loai FROM v_bang_diem;"


**Đã kiểm tra thực tế:** import tệp tạo bảng vào CSDL trống trên MariaDB **11.8.6**, sau đó import dữ liệu giả: 2 lớp, 3 sinh viên, 2 học phần, 4 bản ghi điểm. `SV0001/HP001` cho kết quả **8.60 / Giỏi**; các trường hợp sai FK, điểm `11`, học kỳ `4`, trùng PK đều bị từ chối. 