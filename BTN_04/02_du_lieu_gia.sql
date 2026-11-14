
USE qlsv_diem_hp;

INSERT INTO lop (ma_lop, ten_lop, khoa_hoc, nganh) VALUES
('L26A', 'Lớp minh họa A', 2026, 'Công nghệ thông tin'),
('L26B', 'Lớp minh họa B', 2026, 'Hệ thống thông tin');

INSERT INTO sinhvien (ma_sv, ho_ten, ma_lop, ngay_nhap_hoc, trang_thai) VALUES
('SV0001', 'Sinh viên giả lập 01', 'L26A', '2026-09-01', 'dang_hoc'),
('SV0002', 'Sinh viên giả lập 02', 'L26A', '2026-09-01', 'dang_hoc'),
('SV0003', 'Sinh viên giả lập 03', 'L26B', '2026-09-01', 'dang_hoc');

INSERT INTO hocphan (ma_hp, ten_hp, so_tin_chi) VALUES
('HP001', 'Cơ sở dữ liệu', 3),
('HP002', 'Phân tích thiết kế hệ thống', 3);

INSERT INTO diem (ma_sv, ma_hp, nam_hoc, hoc_ky, diem_qua_trinh, diem_thi) VALUES
('SV0001', 'HP001', 2026, 1, 8.00, 9.00),
('SV0001', 'HP002', 2026, 1, 7.00, 8.00),
('SV0002', 'HP001', 2026, 1, 5.00, 6.00),
('SV0003', 'HP002', 2026, 1, 3.00, 4.00);

-- Truy van mau: bang diem theo sinh vien va ky hoc.
SELECT ma_sv, ma_hp, nam_hoc, hoc_ky, diem_tong_ket, xep_loai, ket_qua
FROM v_bang_diem
WHERE ma_sv = 'SV0001' AND nam_hoc = 2026 AND hoc_ky = 1
ORDER BY ma_hp;
