
CREATE DATABASE IF NOT EXISTS btcn_ql_sinhvien_2023a1173
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE btcn_ql_sinhvien_2023a1173;

-- Bảng danh mục lớp học (thống nhất kiểu dữ liệu khóa chính/khóa ngoại với Thành viên 1 & Thành viên 3)
CREATE TABLE IF NOT EXISTS lop (
  ma_lop VARCHAR(20) PRIMARY KEY,
  ten_lop VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- Bảng sinhvien do Thành viên 2 đảm nhận CRUD
-- Cột is_deleted (0 = đang hoạt động, 1 = trong thùng rác) giúp hỗ trợ tính năng Thùng rác giống giao diện mẫu
CREATE TABLE IF NOT EXISTS sinhvien (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_sv VARCHAR(20) NOT NULL,
  ho_ten VARCHAR(120) NOT NULL,
  ma_lop VARCHAR(20) NOT NULL,
  is_deleted TINYINT(1) NOT NULL DEFAULT 0,
  ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT uq_sinhvien_masv UNIQUE (ma_sv),
  CONSTRAINT fk_sinhvien_lop FOREIGN KEY (ma_lop) REFERENCES lop(ma_lop)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Dữ liệu mẫu bảng lop
INSERT IGNORE INTO lop (ma_lop, ten_lop) VALUES
  ('CNTT01', 'Công nghệ thông tin 01'),
  ('CNTT02', 'Công nghệ thông tin 02'),
  ('KTPM01', 'Kỹ thuật phần mềm 01'),
  ('HTTT01', 'Hệ thống thông tin 01');

-- Dữ liệu giả bảng sinhvien (4 bản ghi hoạt động + 1 bản ghi mẫu trong thùng rác để kiểm thử)
INSERT IGNORE INTO sinhvien (id, ma_sv, ho_ten, ma_lop, is_deleted) VALUES
  (1, 'SV001', 'Nguyễn Minh Anh', 'CNTT01', 0),
  (2, 'SV002', 'Trần Quốc Bảo', 'CNTT02', 0),
  (3, 'SV003', 'Lê Thu Hà', 'CNTT01', 0),
  (4, 'SV004', 'Phạm Hoàng Nam', 'KTPM01', 0),
  (5, 'SV005', 'Đỗ Gia Huy', 'HTTT01', 1);
