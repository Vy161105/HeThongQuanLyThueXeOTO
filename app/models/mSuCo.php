
<?php

class mSuCo
{
    private $con;

    public function __construct($con)
    {
        $this->con = $con;
    }

    // Lấy danh sách sự cố từ database của nhóm
    public function LayDanhSachSuCo()
    {
        $sql = "
            SELECT
                sc.MaSC,
                sc.MaHD,
                hd.SoHD,
                kh.HovaTen AS TenKhachHang,
                sc.LoaiSuCo,
                sc.MoTaSuCo,
                sc.ViTri,
                sc.ThoiDiem,
                sc.ChiPhiPhatPhatSinh,
                sc.PhuongAnXuLy,
                sc.SuDungCuuHo,
                sc.DonViSuaChua
            FROM SuCo AS sc
            INNER JOIN HopDongThue AS hd
                ON sc.MaHD = hd.MaHD
            INNER JOIN KhachHang AS kh
                ON hd.MaKH = kh.MaKH
            ORDER BY sc.MaSC DESC
        ";

        $result = mysqli_query($this->con, $sql);

        if (!$result) {
            error_log(mysqli_error($this->con));
            return false;
        }

        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    // Lấy chi tiết một sự cố theo mã
    public function LayChiTietSuCo($maSC)
    {
        $sql = "
            SELECT
                sc.*,
                hd.SoHD,
                hd.TrangThaiHopDong,
                kh.HovaTen AS TenKhachHang
            FROM SuCo AS sc
            INNER JOIN HopDongThue AS hd
                ON sc.MaHD = hd.MaHD
            INNER JOIN KhachHang AS kh
                ON hd.MaKH = kh.MaKH
            WHERE sc.MaSC = ?
        ";

        $stmt = mysqli_prepare($this->con, $sql);

        if (!$stmt) {
            error_log(mysqli_error($this->con));
            return false;
        }

        mysqli_stmt_bind_param($stmt, "i", $maSC);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        $suCo = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        return $suCo ?: false;
    }

public function LayHopDongDangThue()
{
    $sql = "
        SELECT
            hd.MaHD,
            hd.SoHD,
            hd.MaKH,
            kh.HovaTen AS TenKhachHang,
            hd.NgayBatDau,
            hd.NgayTra,
            hd.TrangThaiHopDong,
            dd.MaXe
        FROM hopdongthue AS hd
        INNER JOIN khachhang AS kh
            ON hd.MaKH = kh.MaKH
        LEFT JOIN dondatxe AS dd
            ON hd.MaDD = dd.MaDD
        WHERE hd.TrangThaiHopDong = 'ConHieuLuc'
          AND hd.NgayBatDau <= NOW()
          AND hd.NgayTra >= NOW()
        ORDER BY hd.MaHD DESC
    ";

    $result = mysqli_query($this->con, $sql);

    if (!$result) {
        error_log(mysqli_error($this->con));
        return false;
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

public function LayHopDongTheoMa($maHD)
{
    $sql = "
        SELECT
            MaHD,
            SoHD,
            NgayBatDau,
            NgayTra,
            TrangThaiHopDong
        FROM hopdongthue
        WHERE MaHD = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($this->con, $sql);

    mysqli_stmt_bind_param($stmt, "i", $maHD);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $hopDong = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    return $hopDong ?: false;
}

public function ThemSuCo($duLieu)
{
    $sql = "
        INSERT INTO suco (
            MaHD,
            LoaiSuCo,
            MoTaSuCo,
            ViTri,
            ThoiDiem
        )
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = mysqli_prepare($this->con, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "issss",
        $duLieu['MaHD'],
        $duLieu['LoaiSuCo'],
        $duLieu['MoTaSuCo'],
        $duLieu['ViTri'],
        $duLieu['ThoiDiem']
    );

    $thanhCong = mysqli_stmt_execute($stmt);

    $maSuCoMoi = $thanhCong
        ? mysqli_insert_id($this->con)
        : false;

    mysqli_stmt_close($stmt);

    return $maSuCoMoi;
}

public function CapNhatXuLySuCo($maSC, $duLieu)
{
    $sql = "
        UPDATE suco
        SET
            PhuongAnXuLy = ?,
            ChiPhiPhatPhatSinh = ?,
            SuDungCuuHo = ?,
            DonViSuaChua = ?
        WHERE MaSC = ?
    ";

    $stmt = mysqli_prepare($this->con, $sql);

    if (!$stmt) {
        error_log(mysqli_error($this->con));
        return false;
    }

    $phuongAn = $duLieu['PhuongAnXuLy'];
    $chiPhi = $duLieu['ChiPhiPhatPhatSinh'];
    $cuuHo = $duLieu['SuDungCuuHo'];
    $donVi = $duLieu['DonViSuaChua'];

    mysqli_stmt_bind_param(
        $stmt,
        "sdisi",
        $phuongAn,
        $chiPhi,
        $cuuHo,
        $donVi,
        $maSC
    );

    $thanhCong = mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    return $thanhCong;
}
}
