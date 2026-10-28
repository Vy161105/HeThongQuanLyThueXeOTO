CREATE DATABASE IF NOT EXISTS quanlythuexeoto
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE quanlythuexeoto;

CREATE TABLE IF NOT EXISTS KHACH_HANG (
    MaKH VARCHAR(10) PRIMARY KEY,
    HoVaTen VARCHAR(100) NOT NULL,
    GioiTinh VARCHAR(10),
    DienThoai VARCHAR(15) NOT NULL UNIQUE,
    SoDinhDanh VARCHAR(20) NOT NULL UNIQUE,
    GiayPhepLaiXe VARCHAR(20) NOT NULL,
    Email VARCHAR(100) UNIQUE,
    DiaChi VARCHAR(255),
    NgayTao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS TAI_KHOAN (
    TenDN VARCHAR(50) PRIMARY KEY,
    MatKhau VARCHAR(255) NOT NULL,
    TrangThaiTaiKhoan VARCHAR(30) NOT NULL DEFAULT 'HoatDong',
    SoLanDangNhapSai INT NOT NULL DEFAULT 0,
    VaiTro VARCHAR(30) NOT NULL DEFAULT 'ThanhVien',
    MaKH VARCHAR(10) NOT NULL UNIQUE,

    CONSTRAINT fk_taikhoan_khachhang
        FOREIGN KEY (MaKH) REFERENCES KHACH_HANG(MaKH),

    CONSTRAINT chk_solan_dangnhap
        CHECK (SoLanDangNhapSai >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS THANH_VIEN (
    MaTV VARCHAR(10) PRIMARY KEY,
    MaKH VARCHAR(10) NOT NULL UNIQUE,
    TinhTrang VARCHAR(30) NOT NULL DEFAULT 'HoatDong',
    NgayBatDau DATE NOT NULL,
    NgayHetHan DATE NOT NULL,
    DiemTichLuy INT NOT NULL DEFAULT 0,

    CONSTRAINT fk_thanhvien_khachhang
        FOREIGN KEY (MaKH) REFERENCES KHACH_HANG(MaKH),

    CONSTRAINT chk_diem_tichluy
        CHECK (DiemTichLuy >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS DON_DAT_XE (
    MaDD VARCHAR(10) PRIMARY KEY,
    MaKH VARCHAR(10) NOT NULL,
    MaMauXe VARCHAR(10) NOT NULL,
    MaNV VARCHAR(10) NULL,
    NgayDat DATE NOT NULL DEFAULT (CURRENT_DATE),
    NgayNhan DATE NOT NULL,
    NgayTra DATE NOT NULL,
    TongGia DECIMAL(15,2) NOT NULL,
    TrangThai VARCHAR(30) NOT NULL DEFAULT 'Chờ xác nhận',
 
    CONSTRAINT fk_dondatxe_khachhang
        FOREIGN KEY (MaKH) REFERENCES KHACH_HANG(MaKH),
 
    CONSTRAINT fk_dondatxe_mauxe
        FOREIGN KEY (MaMauXe) REFERENCES MAU_XE(MaMauXe),
 
    CONSTRAINT fk_dondatxe_nhanvien
        FOREIGN KEY (MaNV) REFERENCES NHAN_VIEN(MaNV),
 
    CONSTRAINT chk_dondatxe_ngaynhan
        CHECK (NgayNhan >= NgayDat),
 
    CONSTRAINT chk_dondatxe_ngaytra
        CHECK (NgayTra > NgayNhan),
 
    CONSTRAINT chk_dondatxe_tonggia
        CHECK (TongGia >= 0),
 
    CONSTRAINT chk_dondatxe_trangthai
        CHECK (TrangThai IN ('Chờ xác nhận', 'Đã xác nhận', 'Từ chối',
                             'Sẵn sàng nhận xe', 'Đã kết thúc', 'Đã hủy'))
) ENGINE=InnoDB;
 
CREATE TABLE IF NOT EXISTS CHI_TIET_DAT_XE (
    MaDD VARCHAR(10) NOT NULL,
    MaXe VARCHAR(10) NOT NULL,
    GhiChu VARCHAR(255) NULL,
 
    PRIMARY KEY (MaDD, MaXe),
 
    CONSTRAINT fk_ctdatxe_dondatxe
        FOREIGN KEY (MaDD) REFERENCES DON_DAT_XE(MaDD),
 
    CONSTRAINT fk_ctdatxe_xe
        FOREIGN KEY (MaXe) REFERENCES XE(MaXe)
) ENGINE=InnoDB;
 
CREATE TABLE IF NOT EXISTS HOP_DONG_THUE (
    MaHD VARCHAR(10) PRIMARY KEY,
    SoHD VARCHAR(20) NOT NULL UNIQUE,
    MaDD VARCHAR(10) NULL,
    MaXe VARCHAR(10) NOT NULL,
    MaNV VARCHAR(10) NOT NULL,
    MaKH VARCHAR(10) NOT NULL,
    NgayLap DATE NOT NULL DEFAULT (CURRENT_DATE),
    NgayBatDau DATE NOT NULL,
    NgayTra DATE NOT NULL,
    TongTien DECIMAL(15,2) NOT NULL,
    TienDatCoc DECIMAL(15,2) NOT NULL DEFAULT 0,
    TienDaThu DECIMAL(15,2) NOT NULL DEFAULT 0,
    NoiDung VARCHAR(500) NULL,
    TrangThaiHopDong VARCHAR(30) NOT NULL DEFAULT 'Đang thuê',
 
    CONSTRAINT fk_hopdong_dondatxe
        FOREIGN KEY (MaDD) REFERENCES DON_DAT_XE(MaDD),
 
    CONSTRAINT fk_hopdong_xe
        FOREIGN KEY (MaXe) REFERENCES XE(MaXe),
 
    CONSTRAINT fk_hopdong_nhanvien
        FOREIGN KEY (MaNV) REFERENCES NHAN_VIEN(MaNV),
 
    CONSTRAINT fk_hopdong_khachhang
        FOREIGN KEY (MaKH) REFERENCES KHACH_HANG(MaKH),
 
    CONSTRAINT chk_hopdong_ngaytra
        CHECK (NgayTra > NgayBatDau),
 
    CONSTRAINT chk_hopdong_tongtien
        CHECK (TongTien >= 0),
 
    CONSTRAINT chk_hopdong_tiendatcoc
        CHECK (TienDatCoc >= 0),
 
    CONSTRAINT chk_hopdong_tiendathu
        CHECK (TienDaThu >= 0),
 
    CONSTRAINT chk_hopdong_trangthai
        CHECK (TrangThaiHopDong IN ('Đang thuê', 'Hoàn tất'))
) ENGINE=InnoDB;