-- =====================================================================
-- Nhóm 09 - Thành viên 5: Quản lý Điểm & Xếp loại (bảng diem)
-- MySQL / MariaDB, utf8mb4, InnoDB. Import vào CSDL trống (phpMyAdmin > Import).
-- Khung 4 bảng thống nhất của nhóm: lop, sinhvien, hocphan, diem.
-- =====================================================================
CREATE DATABASE IF NOT EXISTS quan_ly_diem_tv5
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quan_ly_diem_tv5;

SET NAMES utf8mb4;

-- ---------------------------------------------------------------------
-- 1. lop (TV1)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS lop (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ma_lop    VARCHAR(20)  NOT NULL,
  ten_lop   VARCHAR(100) NOT NULL,
  khoa_hoc  VARCHAR(20)  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_lop_ma_lop (ma_lop)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. sinhvien (TV2) - lop_id là khóa ngoại tới lop.id
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS sinhvien (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ma_sv      VARCHAR(20)  NOT NULL,
  ho_ten     VARCHAR(120) NOT NULL,
  gioi_tinh  VARCHAR(10)  NOT NULL DEFAULT 'Nam',
  ngay_sinh  DATE         NULL,
  lop_id     INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_sinhvien_ma_sv (ma_sv),
  CONSTRAINT fk_sinhvien_lop FOREIGN KEY (lop_id) REFERENCES lop (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. hocphan (TV3)
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS hocphan (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ma_hp       VARCHAR(20)  NOT NULL,
  ten_hp      VARCHAR(120) NOT NULL,
  so_tin_chi  TINYINT UNSIGNED NOT NULL DEFAULT 3,
  PRIMARY KEY (id),
  UNIQUE KEY uq_hocphan_ma_hp (ma_hp),
  CONSTRAINT ck_hocphan_tin_chi CHECK (so_tin_chi > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. diem (TV4 & TV5)
--   diem_qt  : điểm quá trình (0-10), NULL khi chưa nhập
--   diem_thi : điểm thi (0-10), NULL khi chưa nhập
--   diem_tk  : điểm tổng kết = 0.4*diem_qt + 0.6*diem_thi (làm tròn 2 chữ số)
--   xep_loai : A (>=8.5), B (>=7.0), C (>=5.5), D (>=4.0), F (<4.0)
--   trang_thai (vòng đời điểm - bám sát Sơ đồ Trạng thái của TV5):
--       Chua_nhap -> Da_nhap -> Da_khoa
-- ---------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS diem (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  sinhvien_id  INT UNSIGNED NOT NULL,
  hocphan_id   INT UNSIGNED NOT NULL,
  diem_qt      DECIMAL(4,2) NULL,
  diem_thi     DECIMAL(4,2) NULL,
  diem_tk      DECIMAL(4,2) NULL,
  xep_loai     CHAR(1)      NULL,
  trang_thai   VARCHAR(10)  NOT NULL DEFAULT 'Chua_nhap',
  PRIMARY KEY (id),
  UNIQUE KEY uq_diem_sv_hp (sinhvien_id, hocphan_id),
  CONSTRAINT ck_diem_qt   CHECK (diem_qt  IS NULL OR (diem_qt  >= 0 AND diem_qt  <= 10)),
  CONSTRAINT ck_diem_thi  CHECK (diem_thi IS NULL OR (diem_thi >= 0 AND diem_thi <= 10)),
  CONSTRAINT ck_diem_tk   CHECK (diem_tk  IS NULL OR (diem_tk  >= 0 AND diem_tk  <= 10)),
  CONSTRAINT ck_diem_xep_loai   CHECK (xep_loai IS NULL OR xep_loai IN ('A','B','C','D','F')),
  CONSTRAINT ck_diem_trang_thai CHECK (trang_thai IN ('Chua_nhap','Da_nhap','Da_khoa')),
  CONSTRAINT fk_diem_sinhvien FOREIGN KEY (sinhvien_id) REFERENCES sinhvien (id)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_diem_hocphan FOREIGN KEY (hocphan_id) REFERENCES hocphan (id)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Dữ liệu mẫu (dữ liệu giả)
-- ---------------------------------------------------------------------
INSERT IGNORE INTO lop (id, ma_lop, ten_lop, khoa_hoc) VALUES
  (1, 'CNTT01', 'Công nghệ thông tin 01', 'K62'),
  (2, 'CNTT02', 'Công nghệ thông tin 02', 'K62'),
  (3, 'KT01',   'Kế toán 01',             'K63');

INSERT IGNORE INTO sinhvien (id, ma_sv, ho_ten, gioi_tinh, ngay_sinh, lop_id) VALUES
  (1, 'SV001', 'Nguyễn Minh Anh', 'Nữ',  '2004-03-12', 1),
  (2, 'SV002', 'Trần Quốc Bảo',   'Nam', '2004-07-25', 2),
  (3, 'SV003', 'Lê Thu Hà',       'Nữ',  '2004-11-02', 1),
  (4, 'SV004', 'Phạm Đức Long',   'Nam', '2005-01-18', 3);

INSERT IGNORE INTO hocphan (id, ma_hp, ten_hp, so_tin_chi) VALUES
  (1, 'WEB101', 'Thiết kế Web',    3),
  (2, 'DBS101', 'Cơ sở dữ liệu',   3),
  (3, 'PHP101', 'Lập trình PHP',   4);

-- Điểm mẫu: đủ 3 trạng thái và các mức xếp loại; diem_tk/xep_loai khớp công thức.
INSERT IGNORE INTO diem (id, sinhvien_id, hocphan_id, diem_qt, diem_thi, diem_tk, xep_loai, trang_thai) VALUES
  (1, 1, 1, 8.00, 9.00, 8.60, 'A', 'Da_khoa'),
  (2, 1, 2, 7.00, 6.50, 6.70, 'C', 'Da_nhap'),
  (3, 2, 1, 9.00, 7.00, 7.80, 'B', 'Da_nhap'),
  (4, 2, 3, NULL, NULL, NULL, NULL, 'Chua_nhap'),
  (5, 3, 2, 4.00, 3.00, 3.40, 'F', 'Da_nhap'),
  (6, 3, 3, 6.00, 5.00, 5.40, 'D', 'Da_khoa');
