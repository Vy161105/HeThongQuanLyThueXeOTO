<?php

class mKhachHang
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function KiemTraTonTai($dienThoai, $soDinhDanh, $giayPhepLaiXe, $email)
    {
        $sql = "SELECT MaKH FROM KHACH_HANG
                WHERE DienThoai = ?
                   OR SoDinhDanh = ?
                   OR GiayPhepLaiXe = ?";

        if (!empty($email)) {
            $sql .= " OR Email = ?";
        }

        $stmt = mysqli_prepare($this->con, $sql);

        if (!empty($email)) {
            mysqli_stmt_bind_param(
                $stmt,
                "ssss",
                $dienThoai,
                $soDinhDanh,
                $giayPhepLaiXe,
                $email
            );
        } else {
            mysqli_stmt_bind_param(
                $stmt,
                "sss",
                $dienThoai,
                $soDinhDanh,
                $giayPhepLaiXe
            );
        }

        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        $tonTai = mysqli_stmt_num_rows($stmt) > 0;

        mysqli_stmt_close($stmt);

        return $tonTai;
    }

    public function ThemKhachHang($data)
    {
        $sql = "INSERT INTO KHACH_HANG
                (MaKH, HoVaTen, GioiTinh, DienThoai, SoDinhDanh,
                 GiayPhepLaiXe, Email, DiaChi)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($this->con, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssssss",
            $data['MaKH'],
            $data['HoVaTen'],
            $data['GioiTinh'],
            $data['DienThoai'],
            $data['SoDinhDanh'],
            $data['GiayPhepLaiXe'],
            $data['Email'],
            $data['DiaChi']
        );

        $ketQua = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $ketQua;
    }

    public function LayTheoMa($maKH)
    {
        $sql = "SELECT MaKH, HoVaTen, DienThoai, GiayPhepLaiXe
                FROM KHACH_HANG
                WHERE MaKH = ?";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $maKH);
        mysqli_stmt_execute($stmt);

        $ketQua = mysqli_stmt_get_result($stmt);
        $khachHang = mysqli_fetch_assoc($ketQua);

        mysqli_stmt_close($stmt);

        return $khachHang ?: null;
    }
}
