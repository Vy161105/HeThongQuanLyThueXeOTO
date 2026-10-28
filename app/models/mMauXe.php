<?php

class mMauXe
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    public function LayDanhSach()
    {
        $sql = "SELECT m.MaMauXe, m.TenMauXe, m.Gia, p.TenPhanKhuc
                FROM MAU_XE m
                LEFT JOIN PHAN_KHUC_XE p ON p.MaPhanKhuc = m.MaPhanKhuc
                ORDER BY m.TenMauXe";

        $ketQua = mysqli_query($this->con, $sql);
        $dsMauXe = [];

        while ($dong = mysqli_fetch_assoc($ketQua)) {
            $dsMauXe[] = $dong;
        }

        return $dsMauXe;
    }

    public function LayTheoMa($maMauXe, $khoaDong = false)
    {
        $sql = "SELECT MaMauXe, TenMauXe, Gia FROM MAU_XE WHERE MaMauXe = ?";

        if ($khoaDong) {
            // Khóa dòng mẫu xe để hai khách đặt cùng lúc không vượt số xe còn trống.
            $sql .= " FOR UPDATE";
        }

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param($stmt, "s", $maMauXe);
        mysqli_stmt_execute($stmt);

        $ketQua = mysqli_stmt_get_result($stmt);
        $mauXe = mysqli_fetch_assoc($ketQua);

        mysqli_stmt_close($stmt);

        return $mauXe ?: null;
    }

    /**
     * Số xe của mẫu còn trống trong khoảng [ngayNhan, ngayTra).
     *
     * = số xe đang hoạt động của mẫu
     *   - số đơn đặt còn hiệu lực của mẫu trùng khoảng thuê
     *   - số hợp đồng 'Đang thuê' trùng khoảng thuê của xe thuộc mẫu
     *     (bỏ qua hợp đồng sinh từ đơn còn hiệu lực để không tính hai lần).
     */
    public function DemXeConTrong($maMauXe, $ngayNhan, $ngayTra)
    {
        $sql = "SELECT
                (SELECT COUNT(*) FROM XE
                  WHERE MaMauXe = ?
                    AND (TinhTrang IS NULL OR TinhTrang NOT IN ('BaoTri', 'NgungHoatDong', 'MatXe'))
                    AND NgayMat IS NULL)
                -
                (SELECT COUNT(*) FROM DON_DAT_XE
                  WHERE MaMauXe = ?
                    AND TrangThai IN ('Chờ xác nhận', 'Đã xác nhận', 'Sẵn sàng nhận xe')
                    AND NgayNhan < ? AND NgayTra > ?)
                -
                (SELECT COUNT(*) FROM HOP_DONG_THUE h
                  JOIN XE x ON x.MaXe = h.MaXe
                  WHERE x.MaMauXe = ?
                    AND h.TrangThaiHopDong = 'Đang thuê'
                    AND h.NgayBatDau < ? AND h.NgayTra > ?
                    AND (h.MaDD IS NULL OR h.MaDD NOT IN (
                          SELECT MaDD FROM DON_DAT_XE
                           WHERE TrangThai IN ('Chờ xác nhận', 'Đã xác nhận', 'Sẵn sàng nhận xe'))))
                AS ConTrong";

        $stmt = mysqli_prepare($this->con, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            "sssssss",
            $maMauXe,
            $maMauXe, $ngayTra, $ngayNhan,
            $maMauXe, $ngayTra, $ngayNhan
        );
        mysqli_stmt_execute($stmt);

        $ketQua = mysqli_stmt_get_result($stmt);
        $dong = mysqli_fetch_assoc($ketQua);

        mysqli_stmt_close($stmt);

        return (int) ($dong['ConTrong'] ?? 0);
    }
}
