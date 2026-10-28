<?php

class mDonDatXe
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    // Đơn còn hiệu lực của cùng khách, cùng mẫu xe, trùng khoảng thuê (bước 7).
    public function TimDonTrung($maKH, $maMauXe, $ngayNhan, $ngayTra)
    {
        $sql = "SELECT MaDD, NgayNhan, NgayTra, TongGia, TrangThai
                FROM DON_DAT_XE
                WHERE MaKH = ?
                  AND MaMauXe = ?
                  AND TrangThai IN ('Chờ xác nhận', 'Đã xác nhận', 'Sẵn sàng nhận xe')
                  AND NgayNhan < ? AND NgayTra > ?
                LIMIT 1";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "ssss", $maKH, $maMauXe, $ngayTra, $ngayNhan);
        mysqli_stmt_execute($stmt);

        $ketQua = mysqli_stmt_get_result($stmt);
        $don = mysqli_fetch_assoc($ketQua);

        mysqli_stmt_close($stmt);

        return $don ?: null;
    }

    // Tạo đơn với trạng thái 'Chờ xác nhận', chưa gán nhân viên (MaNV = NULL).
    public function ThemDon($data)
    {
        $sql = "INSERT INTO DON_DAT_XE
                (MaDD, MaKH, MaMauXe, MaNV, NgayDat, NgayNhan, NgayTra, TongGia, TrangThai)
                VALUES (?, ?, ?, NULL, CURDATE(), ?, ?, ?, 'Chờ xác nhận')";

        $stmt = mysqli_prepare($this->con, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sssssd",
            $data['MaDD'],
            $data['MaKH'],
            $data['MaMauXe'],
            $data['NgayNhan'],
            $data['NgayTra'],
            $data['TongGia']
        );

        $ketQua = mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

        return $ketQua;
    }
}
