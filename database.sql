-- Bộ khung 4 bảng thống nhất của nhóm (MySQL / MariaDB, utf8mb4, InnoDB)
-- Import: phpMyAdmin -> Import, hoặc: mysql -u root < database.sql (CSDL chưa có bảng)
CREATE DATABASE IF NOT EXISTS qldiem
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qldiem;

-- Chạy trên CSDL trống. Tệp KHÔNG chứa lệnh xóa bảng hay xóa CSDL.

CREATE TABLE lop (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    ma_lop   VARCHAR(20)  NOT NULL UNIQUE,
    ten_lop  VARCHAR(100) NOT NULL,
    khoa_hoc VARCHAR(20)  NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE sinhvien (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    ma_sv     VARCHAR(20)  NOT NULL UNIQUE,
    ho_ten    VARCHAR(100) NOT NULL,
    ngay_sinh DATE         NULL,
    gioi_tinh ENUM('Nam','Nu') NULL,
    lop_id    INT NOT NULL,
    CONSTRAINT fk_sinhvien_lop FOREIGN KEY (lop_id) REFERENCES lop(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE hocphan (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    ma_hp       VARCHAR(20)  NOT NULL UNIQUE,
    ten_hp      VARCHAR(100) NOT NULL,
    so_tin_chi  TINYINT      NOT NULL,
    CONSTRAINT ck_hocphan_tc CHECK (so_tin_chi > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE diem (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    sinhvien_id INT NOT NULL,
    hocphan_id  INT NOT NULL,
    diem_qt     DECIMAL(4,2) NULL,
    diem_thi    DECIMAL(4,2) NULL,
    diem_tk     DECIMAL(4,2) NULL,
    xep_loai    CHAR(1)      NULL,
    trang_thai  VARCHAR(10)  NOT NULL DEFAULT 'Chua_nhap',
    CONSTRAINT fk_diem_sinhvien FOREIGN KEY (sinhvien_id) REFERENCES sinhvien(id),
    CONSTRAINT fk_diem_hocphan  FOREIGN KEY (hocphan_id)  REFERENCES hocphan(id),
    CONSTRAINT uq_diem_sv_hp UNIQUE (sinhvien_id, hocphan_id),
    CONSTRAINT ck_diem_qt   CHECK (diem_qt  IS NULL OR diem_qt  BETWEEN 0 AND 10),
    CONSTRAINT ck_diem_thi  CHECK (diem_thi IS NULL OR diem_thi BETWEEN 0 AND 10),
    CONSTRAINT ck_diem_tk   CHECK (diem_tk  IS NULL OR diem_tk  BETWEEN 0 AND 10),
    CONSTRAINT ck_diem_xl   CHECK (xep_loai IS NULL OR xep_loai IN ('A','B','C','D','F')),
    CONSTRAINT ck_diem_tt   CHECK (trang_thai IN ('Chua_nhap','Da_nhap','Da_khoa'))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ===== Dữ liệu mẫu (≥ 3 bản ghi mỗi bảng) =====
INSERT INTO lop (ma_lop, ten_lop, khoa_hoc) VALUES
    ('CNTT-K61', 'Công nghệ thông tin K61', 'K61'),
    ('DTVT-K61', 'Điện tử viễn thông K61',  'K61'),
    ('KTPM-K62', 'Kỹ thuật phần mềm K62',   'K62');

INSERT INTO sinhvien (ma_sv, ho_ten, ngay_sinh, gioi_tinh, lop_id) VALUES
    ('20235001', 'Nguyễn Văn An',  '2005-03-15', 'Nam', 1),
    ('20235002', 'Trần Thị Bình',  '2005-09-02', 'Nu',  1),
    ('20235003', 'Lê Minh Cường',  '2005-07-21', 'Nam', 2),
    ('20245001', 'Phạm Thu Dung',  '2006-01-10', 'Nu',  3);

INSERT INTO hocphan (ma_hp, ten_hp, so_tin_chi) VALUES
    ('LTW01', 'Lập trình ứng dụng Web', 3),
    ('CSDL01', 'Cơ sở dữ liệu',          3),
    ('MMT01', 'Mạng máy tính',           2);

-- diem_tk = qt*0.4 + thi*0.6 (làm tròn 1 số lẻ); xếp loại A>=8.5, B>=7.0, C>=5.5, D>=4.0, F<4.0
INSERT INTO diem (sinhvien_id, hocphan_id, diem_qt, diem_thi, diem_tk, xep_loai, trang_thai) VALUES
    (1, 1, 8.0, 9.0, 8.6, 'A', 'Da_nhap'),
    (2, 1, 6.0, 7.0, 6.6, 'C', 'Da_nhap'),
    (3, 2, 5.0, 4.0, 4.4, 'D', 'Da_nhap'),
    (1, 2, NULL, NULL, NULL, NULL, 'Chua_nhap'),
    (2, 2, 9.0, 8.0, 8.4, 'B', 'Da_khoa');
