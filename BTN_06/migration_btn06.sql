
USE quan_ly_sinh_vien;
ALTER TABLE diem ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER ngay_tao;
