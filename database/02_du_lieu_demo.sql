-- CHẠY SAU CÙNG (sau 00_danh_muc_xe.sql và oto_database.sql). Dữ liệu mẫu để demo UC08.
USE quanlythuexeoto;

INSERT IGNORE INTO PHAN_KHUC_XE (MaPhanKhuc, TenPhanKhuc) VALUES
('PK01', 'Xe 4-5 chỗ'),
('PK02', 'Xe 7 chỗ'),
('PK03', 'Xe cao cấp');

INSERT IGNORE INTO MAU_XE (MaMauXe, TenMauXe, Gia, MaPhanKhuc) VALUES
('MX01', 'Toyota Vios', 700000, 'PK01'),
('MX02', 'Mitsubishi Xpander', 1000000, 'PK02'),
('MX03', 'Mercedes C300', 2500000, 'PK03');

INSERT IGNORE INTO XE (MaXe, MaMauXe, BienSo, TinhTrang) VALUES
('XE01', 'MX01', '51A-111.11', 'SanSang'),
('XE02', 'MX01', '51A-222.22', 'SanSang'),
('XE03', 'MX02', '51B-333.33', 'SanSang'),
('XE04', 'MX03', '51C-444.44', 'SanSang');

-- Khách hàng demo cho route ?page=demo-dang-nhap (gán $_SESSION['MaKH'] = 'KH01')
INSERT IGNORE INTO KHACH_HANG (MaKH, HoVaTen, GioiTinh, DienThoai, SoDinhDanh, GiayPhepLaiXe, Email, DiaChi) VALUES
('KH01', 'Nguyễn Văn Demo', 'Nam', '0900000001', '012345678901', 'GPLX0001', 'demo@example.com', 'TP.HCM');
