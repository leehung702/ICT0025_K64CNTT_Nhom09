-- BTN 06: Quản lý sinh viên và điểm học phần (thêm, sửa, xóa mềm)

CREATE DATABASE IF NOT EXISTS quan_ly_sinh_vien
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quan_ly_sinh_vien;

CREATE TABLE IF NOT EXISTS lop (
  ma_lop VARCHAR(20) PRIMARY KEY,
  ten_lop VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sinhvien (
  ma_sv INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ho_ten VARCHAR(120) NOT NULL,
  ma_lop VARCHAR(20) NOT NULL,
  CONSTRAINT fk_sinhvien_lop FOREIGN KEY (ma_lop) REFERENCES lop(ma_lop)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hocphan (
  ma_hp VARCHAR(20) PRIMARY KEY,
  ten_hp VARCHAR(120) NOT NULL,
  so_tin_chi TINYINT UNSIGNED NOT NULL DEFAULT 3
) ENGINE=InnoDB;

-- deleted_at: NULL = đang dùng; có giá trị = đã xóa mềm (còn trong CSDL, có thể khôi phục).
CREATE TABLE IF NOT EXISTS diem (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  ma_sv INT UNSIGNED NOT NULL,
  ma_hp VARCHAR(20) NOT NULL,
  diem_so DECIMAL(4,2) NOT NULL,
  ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  deleted_at TIMESTAMP NULL DEFAULT NULL,
  CONSTRAINT uq_sinhvien_hocphan UNIQUE (ma_sv, ma_hp),
  CONSTRAINT ck_diem_so CHECK (diem_so >= 0 AND diem_so <= 10),
  CONSTRAINT fk_diem_sinhvien FOREIGN KEY (ma_sv) REFERENCES sinhvien(ma_sv)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_diem_hocphan FOREIGN KEY (ma_hp) REFERENCES hocphan(ma_hp)
    ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Dữ liệu nền. Bảng diem để trống để kiểm thử danh sách rỗng.
INSERT IGNORE INTO lop (ma_lop, ten_lop) VALUES
  ('CNTT01', 'Công nghệ thông tin 01'),
  ('CNTT02', 'Công nghệ thông tin 02');

INSERT IGNORE INTO sinhvien (ma_sv, ho_ten, ma_lop) VALUES
  (1, 'Nguyễn Minh Anh', 'CNTT01'),
  (2, 'Trần Quốc Bảo', 'CNTT02'),
  (3, 'Lê Thu Hà', 'CNTT01');

INSERT IGNORE INTO hocphan (ma_hp, ten_hp, so_tin_chi) VALUES
  ('WEB101', 'Thiết kế Web', 3),
  ('DBS101', 'Cơ sở dữ liệu', 3),
  ('PHP101', 'Lập trình PHP', 4);
