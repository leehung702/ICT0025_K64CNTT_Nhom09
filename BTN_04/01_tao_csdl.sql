-- BTN_04 | Quan ly sinh vien va diem hoc phan

CREATE DATABASE IF NOT EXISTS qlsv_diem_hp
    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE qlsv_diem_hp;

-- Mot lop co nhieu sinh vien.
CREATE TABLE IF NOT EXISTS lop (
    ma_lop VARCHAR(12) NOT NULL,
    ten_lop VARCHAR(120) NOT NULL,
    khoa_hoc SMALLINT UNSIGNED NOT NULL,
    nganh VARCHAR(120) NOT NULL,
    CONSTRAINT pk_lop PRIMARY KEY (ma_lop),
    CONSTRAINT chk_lop_khoa_hoc CHECK (khoa_hoc BETWEEN 2000 AND 2100)
) ENGINE=InnoDB;

-- Moi sinh vien thuoc dung mot lop.
CREATE TABLE IF NOT EXISTS sinhvien (
    ma_sv VARCHAR(15) NOT NULL,
    ho_ten VARCHAR(120) NOT NULL,
    ma_lop VARCHAR(12) NOT NULL,
    ngay_nhap_hoc DATE NOT NULL,
    trang_thai VARCHAR(20) NOT NULL DEFAULT 'dang_hoc',
    CONSTRAINT pk_sinhvien PRIMARY KEY (ma_sv),
    CONSTRAINT fk_sinhvien_lop FOREIGN KEY (ma_lop) REFERENCES lop (ma_lop)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_sinhvien_trang_thai CHECK (trang_thai IN ('dang_hoc', 'bao_luu', 'tot_nghiep')),
    INDEX idx_sinhvien_ma_lop (ma_lop)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hocphan (
    ma_hp VARCHAR(12) NOT NULL,
    ten_hp VARCHAR(160) NOT NULL,
    so_tin_chi TINYINT UNSIGNED NOT NULL,
    CONSTRAINT pk_hocphan PRIMARY KEY (ma_hp),
    CONSTRAINT chk_hocphan_so_tin_chi CHECK (so_tin_chi BETWEEN 1 AND 10)
) ENGINE=InnoDB;

-- Diem dong thoi bieu dien mot lan hoc cua SV o mot hoc phan/ky/nam.
-- Duoc hoc lai o ky/nam khac; khong co hai diem cho cung mot lan hoc.
CREATE TABLE IF NOT EXISTS diem (
    ma_sv VARCHAR(15) NOT NULL,
    ma_hp VARCHAR(12) NOT NULL,
    nam_hoc SMALLINT UNSIGNED NOT NULL,  -- nam bat dau; VD 2026 la 2026-2027
    hoc_ky TINYINT UNSIGNED NOT NULL,     -- 1, 2 hoac 3
    diem_qua_trinh DECIMAL(4,2) NOT NULL,
    diem_thi DECIMAL(4,2) NOT NULL,
    cap_nhat_luc TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_diem PRIMARY KEY (ma_sv, ma_hp, nam_hoc, hoc_ky),
    CONSTRAINT fk_diem_sinhvien FOREIGN KEY (ma_sv) REFERENCES sinhvien (ma_sv)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_diem_hocphan FOREIGN KEY (ma_hp) REFERENCES hocphan (ma_hp)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_diem_nam_hoc CHECK (nam_hoc BETWEEN 2000 AND 2100),
    CONSTRAINT chk_diem_hoc_ky CHECK (hoc_ky BETWEEN 1 AND 3),
    CONSTRAINT chk_diem_qua_trinh CHECK (diem_qua_trinh BETWEEN 0 AND 10),
    CONSTRAINT chk_diem_thi CHECK (diem_thi BETWEEN 0 AND 10),
    INDEX idx_diem_ma_hp (ma_hp),
    INDEX idx_diem_nam_ky (nam_hoc, hoc_ky)
) ENGINE=InnoDB;

-- Quy uoc minh hoa: diem tong ket = 40% qua trinh + 60% thi.
-- Xep loai: >=9.0 Xuat sac; >=8.0 Gioi; >=6.5 Kha; >=5.0 Trung binh; con lai Yeu. 

CREATE OR REPLACE VIEW v_bang_diem AS
SELECT x.ma_sv, x.ho_ten, x.ma_lop, x.ten_lop,
       x.ma_hp, x.ten_hp, x.so_tin_chi, x.nam_hoc, x.hoc_ky,
       x.diem_qua_trinh, x.diem_thi, x.diem_tong_ket,
       CASE
         WHEN x.diem_tong_ket >= 9.00 THEN 'Xuất sắc'
         WHEN x.diem_tong_ket >= 8.00 THEN 'Giỏi'
         WHEN x.diem_tong_ket >= 6.50 THEN 'Khá'
         WHEN x.diem_tong_ket >= 5.00 THEN 'Trung bình'
         ELSE 'Yếu'
       END AS xep_loai,
       CASE WHEN x.diem_tong_ket >= 5.00 THEN 'Đạt' ELSE 'Không đạt' END AS ket_qua
FROM (
    SELECT s.ma_sv, s.ho_ten, l.ma_lop, l.ten_lop,
           h.ma_hp, h.ten_hp, h.so_tin_chi,
           d.nam_hoc, d.hoc_ky, d.diem_qua_trinh, d.diem_thi,
           ROUND(d.diem_qua_trinh * 0.40 + d.diem_thi * 0.60, 2) AS diem_tong_ket
    FROM diem AS d
    INNER JOIN sinhvien AS s ON s.ma_sv = d.ma_sv
    INNER JOIN lop AS l ON l.ma_lop = s.ma_lop
    INNER JOIN hocphan AS h ON h.ma_hp = d.ma_hp
) AS x;
