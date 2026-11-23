# Quản lý Điểm & Xếp loại – Bài CRUD cá nhân (Thành viên 5, Nhóm 09)

Đề tài nhóm: Quản lý sinh viên và điểm học phần. Phần của Thành viên 5: **bảng `diem` – luồng xếp loại & tra cứu điểm**, tự viết code riêng (không dùng chung source với Thành viên 4).

- Họ và tên: `Sổm Mai Đao Thạ Kun` · MSSV: `2023AN076`
- Nhóm: Nhóm 09 – Thành viên 5
- Đề tài của nhóm: Quản lý sinh viên và điểm học phần
- Phần việc được nhóm phân công (theo PHAN_CONG.md): quản lý **Điểm & Xếp loại** (bảng `diem`, luồng xếp loại và tra cứu điểm); tự viết code riêng, không dùng chung source với Thành viên 4. Phần thiết kế nhóm: Sơ đồ Tuần tự, Sơ đồ Trạng thái.
- Công nghệ: PHP 8.x, MySQL/MariaDB (utf8mb4, InnoDB), PDO, chạy trên XAMPP.

## 1. Cấu trúc thư mục

```
2023AN076_Sổm Mai Đao Thạ Kun_BTCN/
├── src/                      Mã nguồn PHP chạy được
│   ├── index.php             Xem/Tra cứu, Thêm, Sửa, Xóa, Khóa điểm
│   ├── functions.php         Hàm dùng chung (kiểm tra điểm, tính tổng kết, xếp loại, CSRF...)
│   ├── config.example.php    Cấu hình kết nối mẫu (không có mật khẩu thật)
│   └── style.css
├── database.sql              4 bảng lop, sinhvien, hocphan, diem + dữ liệu mẫu
├── README.md                 (file này)
├── BAOCAO_CA_NHAN.md         Lỗi đã tự sửa và lịch sử dùng AI
└── thiet_ke_nhom/            Phần thiết kế nhóm: Sơ đồ Tuần tự, Sơ đồ Trạng thái
```

## 2. Hướng dẫn chạy (XAMPP)

1. Bật **Apache** và **MySQL** trong XAMPP Control Panel.
2. Mở phpMyAdmin → tab **Import** → chọn `database.sql` → **Import** (tự tạo CSDL `quan_ly_diem_tv5`, 4 bảng và dữ liệu mẫu).
3. Chép thư mục `src` vào `C:\xampp\htdocs\` và đổi tên thành `diem_tv5` (hoặc tên bất kỳ).
4. Trong `src`, sao chép `config.example.php` thành `config.php`, sửa `$dbUser`, `$dbPass`, `$dbName` nếu máy bạn khác mặc định (XAMPP: `root`, mật khẩu rỗng). *Nếu bỏ qua bước này, chương trình tự dùng `config.example.php`, đủ để chạy với XAMPP mặc định.*
5. Mở trình duyệt: `http://localhost/diem_tv5/`.

> Không đưa `config.php` chứa mật khẩu thật vào bài nộp; chỉ nộp `config.example.php`.

## 3. Chức năng

| Chức năng | Cách dùng | Ghi chú |
|---|---|---|
| **Xem / Tra cứu** | Bảng điểm có lọc theo mã SV/họ tên, học phần, xếp loại, trạng thái | Hiện số bản ghi, điểm TB, số lượng từng xếp loại; khi lọc đúng 1 sinh viên có thêm ĐTB theo tín chỉ |
| **Thêm** | Chọn sinh viên, học phần, nhập điểm quá trình + điểm thi | Để trống cả hai → trạng thái `Chua_nhap`; nhập đủ → tự tính `diem_tk`, `xep_loai`, trạng thái `Da_nhap` |
| **Sửa / Nhập điểm** | Nút *Sửa* (hoặc *Nhập điểm* với bản ghi Chưa nhập) | Tính lại tổng kết và xếp loại; không sửa được điểm `Da_khoa` |
| **Xóa** | Nút *Xóa* → hộp xác nhận → gửi **form POST** theo `id` | Không xóa được điểm `Da_khoa` |
| **Khóa điểm** | Nút *Khóa* (chỉ ở trạng thái `Da_nhap`) | Chuyển `Da_nhap` → `Da_khoa` |

Công thức: `diem_tk = 0.4 × diem_qt + 0.6 × diem_thi`; xếp loại **A** ≥ 8.5, **B** ≥ 7.0, **C** ≥ 5.5, **D** ≥ 4.0, **F** < 4.0. (Chốt với Thành viên 4; đổi ở `TRONG_SO_QT`, `TRONG_SO_THI`, `xep_loai()` trong `src/functions.php`.)

Vòng đời `trang_thai`: `Chua_nhap` → `Da_nhap` → `Da_khoa` (xem `thiet_ke_nhom/TV5_THIET_KE_NHOM.md`).

## 4. Đáp ứng quy chuẩn bắt buộc

- **PDO + Prepared Statement** cho mọi truy vấn có dữ liệu nhập (kể cả tìm kiếm `LIKE`, có thoát ký tự `%`, `_`).
- **`htmlspecialchars()`** (hàm `e()`) cho mọi dữ liệu in ra HTML.
- **Xóa bằng form POST theo `id`** (không dùng liên kết GET); thêm token **CSRF** cho mọi thao tác ghi.
- Kiểm tra phía server: ID dạng số nguyên dương, điểm trong 0–10, tối đa 2 chữ số thập phân; trùng cặp (sinh viên, học phần) được báo lỗi thân thiện; sau POST thành công chuyển hướng (PRG) nên F5 không gửi lại form.
- Ô nhập điểm cố ý để `type="text"` (không dùng `type="number"`) để dữ liệu sai vẫn đi tới server và được kiểm tra, phục vụ kiểm thử.

## 5. Nhật ký kiểm thử

Mỗi ca ghi: dữ liệu nhập, kết quả mong đợi, kết quả thực tế. Cách kiểm thử tự động: chạy `php -S` (PHP 8.3) và gửi các yêu cầu HTTP thật tới `index.php` bằng script Python, rồi đối chiếu cả thông báo lẫn dữ liệu trong CSDL. **Lưu ý: môi trường kiểm thử này dùng SQLite thay cho MySQL** (cùng cấu trúc bảng và dữ liệu mẫu) vì không có MySQL sẵn. Vì vậy bạn **cần chạy lại các ca dưới đây trên XAMPP/MySQL của bạn** và ghi vào cột cuối trước khi nộp. `database.sql` chưa được import thử trên MySQL thật – hãy import vào CSDL trống và ghi kết quả. Riêng ràng buộc `CHECK` ở `database.sql` chỉ có hiệu lực từ MySQL 8.0.16 / MariaDB 10.2.1 trở lên.

| # | Ca kiểm thử | Đầu vào | Kết quả mong đợi | Kết quả (tự động, SQLite) | Kết quả trên XAMPP của bạn |
|---|---|---|---|---|---|
| 1 | Xem danh sách | Mở trang | Hiện đủ 6 bản ghi mẫu | Đạt | |
| 2 | Thêm hợp lệ | SV004 – WEB101, 8 và 9 | Lưu; tổng kết 8.60, xếp loại A, `Da_nhap` | Đạt | |
| 3 | Thêm để trống điểm | Không nhập điểm | Tạo bản ghi `Chua_nhap` | Đạt | |
| 4 | Dấu phẩy thập phân | `7,5` và `6,5` | Tổng kết 6.90, loại C | Đạt | |
| 5 | Điểm âm | QT = `-1` | Báo "không được âm", không ghi CSDL | Đạt | |
| 6 | Điểm > 10 | Thi = `10.5` | Báo "không được lớn hơn 10" | Đạt | |
| 7 | Không phải số | QT = `abc`, `1e1` | Báo "phải là một số" | Đạt | |
| 8 | Quá 2 chữ số thập phân | QT = `5.555` | Báo lỗi | Đạt | |
| 9 | Chỉ nhập 1 ô điểm | QT = 5, thi trống | Báo phải nhập đủ cả hai | Đạt | |
| 10 | Trùng sinh viên–học phần | SV001 – WEB101 đã có | Báo trùng, số dòng không đổi | Đạt | |
| 11 | Thiếu sinh viên / SV không tồn tại | `sinhvien_id` rỗng / 999 | Báo lỗi tương ứng | Đạt | |
| 12 | Thiếu token CSRF | POST không có `csrf` | Bị từ chối, không ghi CSDL | Đạt | |
| 13 | Nhập điểm cho bản ghi Chưa nhập | id 4: 6 và 7 | `diem_tk` 6.60, loại C, `Da_nhap` | Đạt | |
| 14 | Sửa sai | Thi = 11 | Báo lỗi, dữ liệu cũ giữ nguyên | Đạt | |
| 15 | Sửa thiếu điểm | QT trống | Báo "vui lòng nhập điểm quá trình" | Đạt | |
| 16 | Sửa ID không tồn tại | `id=999999`; `?edit=abc` | Báo không tìm thấy | Đạt | |
| 17 | Sửa điểm đã khóa | id 1 (`Da_khoa`) | Bị chặn, dữ liệu không đổi | Đạt | |
| 18 | Khóa điểm | id 4 (`Da_nhap`) | Chuyển `Da_khoa` | Đạt | |
| 19 | Khóa lần 2 / khóa bản ghi Chưa nhập | | Từ chối, trạng thái không đổi | Đạt | |
| 20 | Xóa điểm đã khóa | id 4 sau khi khóa | Từ chối, số dòng không đổi | Đạt | |
| 21 | Xóa bản ghi Chưa nhập | | Xóa đúng 1 dòng | Đạt | |
| 22 | Xóa ID không tồn tại | `id=999999` | Báo không xóa được | Đạt | |
| 23 | Xóa với ID bất thường | `id="1 OR 1=1"` | Báo ID không hợp lệ, không xóa gì | Đạt | |
| 24 | Xóa bằng GET | `?action=delete&id=2` | Không xóa gì | Đạt | |
| 25 | Tra cứu theo mã/tên | `SV001`, `Minh Anh` | Chỉ hiện đúng sinh viên; có ĐTB theo tín chỉ | Đạt | |
| 26 | Lọc xếp loại / trạng thái | `xl=F`, `tt=Da_khoa`, `xl=CHUA` | Chỉ hiện dòng khớp | Đạt | |
| 27 | Từ khóa không khớp | `zzzz` | "Không có bản ghi nào khớp bộ lọc" | Đạt | |
| 28 | Ký tự đặc biệt LIKE | `%` | Không khớp tràn lan | Đạt | |
| 29 | Chèn SQL qua ô tìm kiếm | `' OR 1=1 --` | Không có bản ghi khớp, không lỗi | Đạt | |
| 30 | XSS | `<script>alert(1)</script>` | Hiển thị dạng văn bản đã escape | Đạt | |
| 32 | Danh sách rỗng | Bảng `diem` không có dòng nào | Hiện "Chưa có dữ liệu điểm." | Đạt | |
| 31 | Công thức và biên xếp loại | (8.5,8.5), (7,7), (5.5,5.5), (4,4), (3.9,3.9), (10,10), (0,0) | A, B, C, D, F, A, F | Đạt | |

Kiểm tra bằng mắt: thao tác xác nhận khi bấm Xóa/Khóa (nút Cancel không gửi yêu cầu) là hộp `confirm()` của trình duyệt, **chưa** được kiểm thử tự động – hãy tự bấm thử và ghi lại.
