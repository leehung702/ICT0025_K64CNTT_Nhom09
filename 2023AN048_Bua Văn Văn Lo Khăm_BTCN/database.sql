CREATE DATABASE IF NOT EXISTS ql_hocphan
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE ql_hocphan;

CREATE TABLE IF NOT EXISTS hocphan (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ma_hp VARCHAR(20) NOT NULL UNIQUE,
    ten_hp VARCHAR(150) NOT NULL,
    so_tin_chi TINYINT UNSIGNED NOT NULL,
    CONSTRAINT chk_hocphan_tinchi CHECK (so_tin_chi > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO hocphan (ma_hp, ten_hp, so_tin_chi) VALUES
('HP001', 'Lập trình PHP', 3),
('HP002', 'Cơ sở dữ liệu', 3),
('HP003', 'Phân tích thiết kế hệ thống', 4)
ON DUPLICATE KEY UPDATE
ten_hp = VALUES(ten_hp), so_tin_chi = VALUES(so_tin_chi);
