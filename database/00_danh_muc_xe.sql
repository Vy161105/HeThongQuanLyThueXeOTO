-- CHẠY FILE NÀY TRƯỚC oto_database.sql
-- oto_database.sql có khóa ngoại tới MAU_XE, XE, NHAN_VIEN nhưng chưa định nghĩa
-- các bảng đó. Cột được suy ra từ các câu truy vấn trong app/models/mMauXe.php;
-- hãy đối chiếu lại với sơ đồ CSDL của nhóm nếu khác.

CREATE DATABASE IF NOT EXISTS quanlythuexeoto
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE quanlythuexeoto;

CREATE TABLE IF NOT EXISTS PHAN_KHUC_XE (
    MaPhanKhuc VARCHAR(10) PRIMARY KEY,
    TenPhanKhuc VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS MAU_XE (
    MaMauXe VARCHAR(10) PRIMARY KEY,
    TenMauXe VARCHAR(100) NOT NULL,
    Gia DECIMAL(15,2) NOT NULL,
    MaPhanKhuc VARCHAR(10) NULL,

    CONSTRAINT fk_mauxe_phankhuc
        FOREIGN KEY (MaPhanKhuc) REFERENCES PHAN_KHUC_XE(MaPhanKhuc),

    CONSTRAINT chk_mauxe_gia CHECK (Gia >= 0)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS XE (
    MaXe VARCHAR(10) PRIMARY KEY,
    MaMauXe VARCHAR(10) NOT NULL,
    BienSo VARCHAR(20) NULL,
    TinhTrang VARCHAR(30) NULL,
    NgayMat DATE NULL,

    CONSTRAINT fk_xe_mauxe
        FOREIGN KEY (MaMauXe) REFERENCES MAU_XE(MaMauXe)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS NHAN_VIEN (
    MaNV VARCHAR(10) PRIMARY KEY,
    HoVaTen VARCHAR(100) NOT NULL
) ENGINE=InnoDB;
