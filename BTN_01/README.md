# Bài thực hành PHP -- Nhóm 9

## Giới thiệu

Dự án gồm 6 trang thực hành PHP cơ bản, tương ứng với các nội dung từ
bài 2.1 đến 2.6. Mỗi trang minh họa một chủ đề riêng, kết hợp PHP với
HTML để hiển thị kết quả trực tiếp trên trình duyệt.

Giao diện các trang sử dụng tiếng Việt, có thanh điều hướng giữa sáu bài
học và phần thử thách nhỏ để người học tự thay đổi mã nguồn.

## Mục tiêu

-   Làm quen với cú pháp PHP và cách nhúng PHP vào HTML.
-   Biết khai báo, sử dụng và cập nhật biến.
-   Phân biệt biến và hằng trong PHP.
-   Nhận biết một số kiểu dữ liệu cơ bản và thao tác ép kiểu.
-   Thực hành toán tử số học, gán, so sánh và logic.
-   Vận dụng kiến thức để tính tiền hàng, phí và tổng thanh toán.

## Công nghệ sử dụng

-   **PHP**: xử lý dữ liệu và tính toán ở phía máy chủ.
-   **HTML5**: xây dựng cấu trúc nội dung trang.
-   **CSS3**: định dạng giao diện, bố cục và thanh điều hướng.

Không thấy dự án sử dụng framework hay thư viện bên ngoài; các trang
được viết bằng PHP, HTML và CSS thuần.

## Cấu trúc thư mục

nhom9/
├── 01_cu_phap.php
├── 02_bien.php
├── 03_hang.php
├── 04_kieu_du_lieu.php
├── 05_toan_tu.php
└── 06_van_dung.php


## Cách chạy dự án

### Cách 1: Dùng máy chủ tích hợp của PHP

1.  Giải nén dự án.

2.  Mở Terminal hoặc Command Prompt tại thư mục chứa các tệp PHP (thư
    mục `nhom9`).

3.  Chạy lệnh:


    php -S localhost:8000
    

4.  Mở trình duyệt và truy cập:

    
    http://localhost:8000/01_cu_phap.php
    

5.  Sử dụng thanh điều hướng trên trang để chuyển giữa sáu bài thực
    hành.

### Cách 2: Dùng XAMPP

1.  Sao chép thư mục `nhom9` vào thư mục `htdocs` của XAMPP.

2.  Khởi động Apache trong XAMPP Control Panel.

3.  Truy cập:

    
    http://localhost/nhom9/01_cu_phap.php
    

## Ví dụ kết quả bài vận dụng

Với dữ liệu mặc định trong `06_van_dung.php`:

-   Mặt hàng: Vở kẻ ngang
-   Số lượng: 3
-   Đơn giá: 25.000 VND
-   Tỷ lệ phí: 5%

Cách tính:


Tiền hàng = Số lượng × Đơn giá
          = 3 × 25.000
          = 75.000 VND

Tiền phí  = Tiền hàng × 5%
          = 75.000 × 0,05
          = 3.750 VND

Tổng tiền = Tiền hàng + Tiền phí
          = 75.000 + 3.750
          = 78.750 VND


Trong bài 06, có thể thử bộ dữ liệu thứ hai bằng cách sửa ba biến đầu
phần PHP: tên hàng thành `Bút bi xanh`, số lượng thành `2` và đơn giá
thành `18000`.

## Hướng phát triển và thực hành thêm

-   Thay đổi giá trị biến và hằng để quan sát kết quả.
-   Bổ sung thêm ví dụ về kiểu dữ liệu và toán tử.
-   Phát triển bài vận dụng thành biểu mẫu nhập tên hàng, số lượng và
    đơn giá.
-   Kiểm tra dữ liệu đầu vào trước khi thực hiện phép tính.
-   Tách phần CSS dùng chung thành một tệp riêng để dễ bảo trì.

