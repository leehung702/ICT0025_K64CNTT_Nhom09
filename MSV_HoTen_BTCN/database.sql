-- BTCN - Thành viên 4: quản lý điểm học phần (bảng diem)
-- MySQL / MariaDB, utf8mb4, InnoDB. Import bằng phpMyAdmin (tab Import).
-- Lưu ý: tệp xóa và tạo lại 4 bảng để có thể import lại nhiều lần.

CREATE DATABASE IF NOT EXISTS qldiem
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qldiem;

DROP TABLE IF EXISTS diem;
DROP TABLE IF EXISTS sinhvien;
DROP TABLE IF EXISTS hocphan;
DROP TABLE IF EXISTS lop;

CREATE TABLE lop (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    ma_lop   VARCHAR(20)  NOT NULL UNIQUE,
    ten_lop  VARCHAR(100) NOT NULL,
    khoa_hoc VARCHAR(20)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE sinhvien (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    ma_sv     VARCHAR(20) NOT NULL UNIQUE,
    ho_ten    VARCHAR(80) NOT NULL,
    ngay_sinh DATE NULL,
    gioi_tinh ENUM('Nam','Nu','Khac') NULL,
    lop_id    INT NOT NULL,
    CONSTRAINT fk_sinhvien_lop FOREIGN KEY (lop_id)
        REFERENCES lop(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE hocphan (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    ma_hp      VARCHAR(20)  NOT NULL UNIQUE,
    ten_hp     VARCHAR(100) NOT NULL,
    so_tin_chi TINYINT UNSIGNED NOT NULL,
    CONSTRAINT chk_hocphan_tc CHECK (so_tin_chi > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE diem (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sinhvien_id INT NOT NULL,
    hocphan_id  INT NOT NULL,
    diem_qt     DECIMAL(4,2) NULL,
    diem_thi    DECIMAL(4,2) NULL,
    diem_tk     DECIMAL(3,1) NULL,
    xep_loai    ENUM('A','B','C','D','F') NULL,
    trang_thai  ENUM('Chua_nhap','Da_nhap','Da_khoa') NOT NULL DEFAULT 'Chua_nhap',
    created     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_diem_sv_hp (sinhvien_id, hocphan_id),
    CONSTRAINT chk_diem_qt  CHECK (diem_qt  IS NULL OR diem_qt  BETWEEN 0 AND 10),
    CONSTRAINT chk_diem_thi CHECK (diem_thi IS NULL OR diem_thi BETWEEN 0 AND 10),
    CONSTRAINT chk_diem_tk  CHECK (diem_tk  IS NULL OR diem_tk  BETWEEN 0 AND 10),
    CONSTRAINT fk_diem_sinhvien FOREIGN KEY (sinhvien_id)
        REFERENCES sinhvien(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_diem_hocphan FOREIGN KEY (hocphan_id)
        REFERENCES hocphan(id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dữ liệu mẫu (giả)
INSERT INTO lop (ma_lop, ten_lop, khoa_hoc) VALUES
('K61-CNTT', 'Công nghệ thông tin K61', '2023'),
('K61-KTPM', 'Kỹ thuật phần mềm K61',   '2023'),
('K62-CNTT', 'Công nghệ thông tin K62', '2024');

INSERT INTO sinhvien (ma_sv, ho_ten, ngay_sinh, gioi_tinh, lop_id) VALUES
('20235001', 'Nguyễn Văn A', '2005-03-15', 'Nam', 1),
('20235002', 'Trần Thị B',   '2005-07-21', 'Nu',  1),
('20235003', 'Lê Văn C',     '2005-11-02', 'Nam', 2);

INSERT INTO hocphan (ma_hp, ten_hp, so_tin_chi) VALUES
('LTW01',  'Lập trình ứng dụng web', 3),
('CSDL01', 'Cơ sở dữ liệu',          3),
('MMT01',  'Mạng máy tính',          2);

-- diem_tk = 0,4 x diem_qt + 0,6 x diem_thi (làm tròn 1 chữ số)
INSERT INTO diem (sinhvien_id, hocphan_id, diem_qt, diem_thi, diem_tk, xep_loai, trang_thai) VALUES
(1, 1, 8.00, 9.00, 8.6, 'A',  'Da_nhap'),
(1, 2, 7.00, 6.00, 6.4, 'C',  'Da_nhap'),
(2, 1, 5.00, 3.00, 3.8, 'F',  'Da_nhap'),
(3, 1, 9.00, 7.50, 8.1, 'B',  'Da_khoa'),
(2, 2, NULL, NULL, NULL, NULL, 'Chua_nhap');
