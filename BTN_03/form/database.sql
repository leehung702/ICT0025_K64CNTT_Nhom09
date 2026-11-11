CREATE DATABASE IF NOT EXISTS form_cccd_demo
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE form_cccd_demo;

CREATE TABLE IF NOT EXISTS nguoi_dung (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    ma_ho_so VARCHAR(20) NOT NULL,
    ho_ten VARCHAR(150) NOT NULL,
    ngay_sinh DATE NOT NULL,
    gioi_tinh VARCHAR(10) NOT NULL,
    quoc_tich VARCHAR(50) NOT NULL,
    que_quan VARCHAR(255) NOT NULL,
    dia_chi_thuong_tru VARCHAR(500) NOT NULL,
    dia_chi_hien_tai VARCHAR(500) NOT NULL,
    so_dien_thoai VARCHAR(20) NOT NULL,
    email VARCHAR(190) NOT NULL,
    mat_khau VARCHAR(255) NOT NULL,
    ngay_tao TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_nguoi_dung_ma_ho_so (ma_ho_so),
    UNIQUE KEY uq_nguoi_dung_email (email)
) ENGINE=InnoDB
  DEFAULT CHARACTER SET=utf8mb4
  COLLATE=utf8mb4_unicode_ci;
