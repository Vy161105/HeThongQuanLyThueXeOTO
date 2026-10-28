<?php

require_once __DIR__ . '/../../config/Connect.php';
require_once __DIR__ . '/../models/mKhachHang.php';
require_once __DIR__ . '/../models/mMauXe.php';
require_once __DIR__ . '/../models/mDonDatXe.php';

class cDatXe
{
    private $con;
    private $khachHang;
    private $mauXe;
    private $donDatXe;

    public function __construct()
    {
        $ketNoi = new Connect();
        $this->con = $ketNoi->Ketnoi();

        $this->khachHang = new mKhachHang($this->con);
        $this->mauXe = new mMauXe($this->con);
        $this->donDatXe = new mDonDatXe($this->con);
    }

    public function LayDanhSachMauXe(): array
    {
        return $this->mauXe->LayDanhSach();
    }

    // Bước 2: kiểm tra phiên đăng nhập, mẫu xe và dựng dữ liệu cho biểu mẫu.
    public function LayBieuMau($maKH, $maMauXe): array
    {
        if (empty($maKH)) {
            return ['success' => false, 'code' => 'HET_PHIEN',
                'message' => 'Vui lòng đăng nhập để đặt thuê xe.'];
        }

        $khachHang = $this->khachHang->LayTheoMa($maKH);

        if ($khachHang === null) {
            return ['success' => false, 'code' => 'HET_PHIEN',
                'message' => 'Phiên đăng nhập không còn hiệu lực. Vui lòng đăng nhập lại.'];
        }

        $mauXe = $this->mauXe->LayTheoMa($maMauXe);

        if ($mauXe === null) {
            return ['success' => false, 'code' => 'MAU_XE',
                'message' => 'Mẫu xe không còn tồn tại. Vui lòng chọn lại.'];
        }

        return ['success' => true, 'khachHang' => $khachHang, 'mauXe' => $mauXe];
    }

    // Bước 4-9: kiểm tra, tạo và lưu đơn đặt xe.
    public function DatXe($maKH, $maMauXe, array $data): array
    {
        $bieuMau = $this->LayBieuMau($maKH, $maMauXe);

        if (!$bieuMau['success']) {
            return $bieuMau;
        }

        $khachHang = $bieuMau['khachHang'];
        $mauXe = $bieuMau['mauXe'];

        $ngayNhan = trim($data['NgayNhan'] ?? '');
        $ngayTra = trim($data['NgayTra'] ?? '');

        // Bước 5: kiểm tra ngày nhận / ngày trả.
        if ($ngayNhan === '') {
            return ['success' => false, 'code' => 'DU_LIEU', 'field' => 'NgayNhan',
                'message' => 'Vui lòng chọn ngày nhận xe.'];
        }

        if ($ngayTra === '') {
            return ['success' => false, 'code' => 'DU_LIEU', 'field' => 'NgayTra',
                'message' => 'Vui lòng chọn ngày trả xe.'];
        }

        $dNhan = DateTime::createFromFormat('Y-m-d', $ngayNhan);
        $dTra = DateTime::createFromFormat('Y-m-d', $ngayTra);

        if (
            !$dNhan || $dNhan->format('Y-m-d') !== $ngayNhan
            || !$dTra || $dTra->format('Y-m-d') !== $ngayTra
        ) {
            return ['success' => false, 'code' => 'DU_LIEU', 'field' => 'NgayNhan',
                'message' => 'Định dạng ngày không hợp lệ.'];
        }

        $homNay = new DateTime('today');

        if ($dNhan < $homNay) {
            return ['success' => false, 'code' => 'DU_LIEU', 'field' => 'NgayNhan',
                'message' => 'Ngày nhận không hợp lệ: không được trước ngày hiện tại.'];
        }

        if ($dTra <= $dNhan) {
            return ['success' => false, 'code' => 'DU_LIEU', 'field' => 'NgayTra',
                'message' => 'Thời gian thuê không hợp lệ: ngày trả phải sau ngày nhận.'];
        }

        // Bước 5 (tiếp): giấy phép lái xe trong hồ sơ.
        // Lưu ý: bảng KHACH_HANG chưa có cột ngày hết hạn GPLX nên chỉ kiểm tra
        // GPLX đã được khai báo trong hồ sơ.
        if (trim($khachHang['GiayPhepLaiXe'] ?? '') === '') {
            return ['success' => false, 'code' => 'GPLX',
                'message' => 'Thông tin giấy phép lái xe trong hồ sơ thiếu hoặc hết hiệu lực. Vui lòng cập nhật hồ sơ trước khi đặt xe.'];
        }

        // Bước 6: còn xe đáp ứng khoảng thời gian thuê.
        if ($this->mauXe->DemXeConTrong($maMauXe, $ngayNhan, $ngayTra) <= 0) {
            return ['success' => false, 'code' => 'HET_XE',
                'message' => 'Mẫu xe này không đáp ứng được thời gian thuê. Vui lòng đổi thời gian hoặc chọn mẫu xe khác.'];
        }

        // Bước 7: đơn còn hiệu lực trùng mẫu xe và khoảng thuê.
        $donTrung = $this->donDatXe->TimDonTrung($maKH, $maMauXe, $ngayNhan, $ngayTra);

        if ($donTrung !== null) {
            return ['success' => false, 'code' => 'TRUNG_DON', 'donTrung' => $donTrung,
                'message' => 'Bạn đã có đơn đặt còn hiệu lực cho mẫu xe này trong khoảng thời gian trên.'];
        }

        // Bước 8: xác định giá, tạo và lưu đơn (Hậu điều kiện: không ghi đơn dở dang).
        $soNgay = (int) $dNhan->diff($dTra)->days;
        $tongGia = $soNgay * (float) $mauXe['Gia'];
        $maDD = 'DD' . bin2hex(random_bytes(4));

        try {
            mysqli_begin_transaction($this->con);

            // Khóa mẫu xe rồi kiểm tra lại để tránh hai đơn tranh cùng chiếc xe cuối.
            $this->mauXe->LayTheoMa($maMauXe, true);

            if ($this->mauXe->DemXeConTrong($maMauXe, $ngayNhan, $ngayTra) <= 0) {
                mysqli_rollback($this->con);

                return ['success' => false, 'code' => 'HET_XE',
                    'message' => 'Mẫu xe này không đáp ứng được thời gian thuê. Vui lòng đổi thời gian hoặc chọn mẫu xe khác.'];
            }

            $daThem = $this->donDatXe->ThemDon([
                'MaDD' => $maDD,
                'MaKH' => $maKH,
                'MaMauXe' => $maMauXe,
                'NgayNhan' => $ngayNhan,
                'NgayTra' => $ngayTra,
                'TongGia' => $tongGia
            ]);

            if (!$daThem) {
                throw new Exception('Khong them duoc don dat xe.');
            }

            mysqli_commit($this->con);

            return [
                'success' => true,
                'maDD' => $maDD,
                'soNgay' => $soNgay,
                'tongGia' => $tongGia,
                'message' => 'Đặt thuê xe thành công.'
            ];
        } catch (Throwable $e) {
            mysqli_rollback($this->con);
            error_log($e->getMessage());

            return ['success' => false, 'code' => 'LOI_CSDL',
                'message' => 'Đặt thuê xe thất bại. Vui lòng thử lại.'];
        }
    }
}
