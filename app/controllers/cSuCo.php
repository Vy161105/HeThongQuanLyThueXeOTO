
<?php

require_once __DIR__ . '/../../config/Connect.php';
require_once __DIR__ . '/../models/mSuCo.php';

class cSuCo
{
    private $con;
    private $suCo;

    public function __construct()
    {
        // Tạo kết nối giống Controller đăng ký của nhóm
        $ketNoi = new Connect();
        $this->con = $ketNoi->Ketnoi();

        // Truyền kết nối MySQLi vào Model sự cố
        $this->suCo = new mSuCo($this->con);
    }

    // Chức năng xem danh sách sự cố
    public function DanhSachSuCo()
    {
        return $this->suCo->LayDanhSachSuCo();
    }

    // Chức năng xem chi tiết một sự cố
    public function ChiTietSuCo($maSC)
    {
        // Kiểm tra mã sự cố
        $maSC = filter_var(
            $maSC,
            FILTER_VALIDATE_INT
        );

        if ($maSC === false || $maSC <= 0) {
            return false;
        }

        // Gọi Model lấy dữ liệu
        return $this->suCo->LayChiTietSuCo($maSC);
    }

public function HopDongDangThue()
{
    return $this->suCo->LayHopDongDangThue();
}

public function GhiNhanSuCo($duLieu)
{
    $maHD = filter_var(
        $duLieu['MaHD'] ?? null,
        FILTER_VALIDATE_INT
    );

    $loaiSuCo = trim($duLieu['LoaiSuCo'] ?? '');
    $moTa = trim($duLieu['MoTaSuCo'] ?? '');
    $viTri = trim($duLieu['ViTri'] ?? '');
    
    if (mb_strlen($viTri, 'UTF-8') < 10) {
        return [
            'success' => false,
            'message' => 'Vị trí xảy ra sự cố phải có ít nhất 10 ký tự.'
        ];
    }

    if (
        !preg_match('/\p{L}/u', $viTri) ||
        !preg_match('/[\s,\/\-]/u', $viTri)
    ) {
        return [
            'success' => false,
            'message' =>
                'Vui lòng nhập vị trí rõ ràng hơn, gồm tên đường, khu vực hoặc địa điểm.'
        ];
    }

    $thoiDiem = trim($duLieu['ThoiDiem'] ?? '');

    // 1. Kiểm tra thông tin bắt buộc
    if (!$maHD || !$loaiSuCo || !$moTa || !$viTri || !$thoiDiem) {
        return [
            'success' => false,
            'message' => 'Vui lòng nhập đầy đủ thông tin bắt buộc.'
        ];
    }

    // 2. Kiểm tra độ dài và loại sự cố
    $loaiHopLe = [
        'Mat chia khoa',
        'Va cham nhe',
        'Tai nan nghiem trong',
        'Khac'
    ];

    if (
        !in_array($loaiSuCo, $loaiHopLe, true) ||
        mb_strlen($viTri, 'UTF-8') > 255 ||
        mb_strlen($moTa, 'UTF-8') > 5000
    ) {
        return [
            'success' => false,
            'message' => 'Thông tin sự cố không hợp lệ.'
        ];
    }

    // 3. Kiểm tra hợp đồng tồn tại
    $hopDong = $this->suCo->LayHopDongTheoMa($maHD);

    if (!$hopDong) {
        return [
            'success' => false,
            'message' => 'Hợp đồng thuê xe không tồn tại.'
        ];
    }

    try {
        $batDau = new DateTimeImmutable($hopDong['NgayBatDau']);
        $ngayTra = new DateTimeImmutable($hopDong['NgayTra']);

        // datetime-local có định dạng YYYY-MM-DDTHH:MM
        $ngaySuCo = DateTimeImmutable::createFromFormat(
            '!Y-m-d\TH:i',
            $thoiDiem
        );

        if (
            !$ngaySuCo ||
            $ngaySuCo->format('Y-m-d\TH:i') !== $thoiDiem
        ) {
            throw new Exception('Thời gian không hợp lệ.');
        }

        $hienTai = new DateTimeImmutable();

        // 4. Kiểm tra hợp đồng đang có hiệu lực
        if (
            $hopDong['TrangThaiHopDong'] !== 'ConHieuLuc' ||
            $batDau > $hienTai ||
            $ngayTra < $hienTai
        ) {
            return [
                'success' => false,
                'message' => 'Hợp đồng không còn hiệu lực thuê xe.'
            ];
        }

        // 5. Thời điểm sự cố phải thuộc thời hạn thuê
        
if ($ngaySuCo > $hienTai) {
    return [
        'success' => false,
        'message' => 'Thời điểm xảy ra sự cố không được lớn hơn thời gian hiện tại.'
    ];
}

if ($ngaySuCo < $batDau || $ngaySuCo > $ngayTra) {
    return [
        'success' => false,
        'message' => 'Thời điểm sự cố phải nằm trong thời hạn thuê xe của hợp đồng.'
    ];
}


    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => 'Dữ liệu thời gian không hợp lệ.'
        ];
    }

    // 6. Chuẩn bị dữ liệu để lưu
    $duLieuLuu = [
        'MaHD' => (int)$maHD,
        'LoaiSuCo' => $loaiSuCo,
        'MoTaSuCo' => $moTa,
        'ViTri' => $viTri,
        'ThoiDiem' => $ngaySuCo->format('Y-m-d H:i:s')
    ];

    // 7. Gọi Model INSERT vào bảng suco
    try {
        $maSuCoMoi = $this->suCo->ThemSuCo($duLieuLuu);
    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage());
        $maSuCoMoi = false;
    }

    if (!$maSuCoMoi) {
        return [
            'success' => false,
            'message' => 'Không thể lưu sự cố. Vui lòng thử lại.'
        ];
    }

    return [
        'success' => true,
        'message' => 'Ghi nhận sự cố thành công!',
        'MaSC' => $maSuCoMoi
    ];
}

public function XuLySuCo($maSC, $duLieu)
{
    // 1. Kiểm tra mã sự cố
    $maSC = filter_var($maSC, FILTER_VALIDATE_INT);

    if ($maSC === false || $maSC <= 0) {
        return [
            'success' => false,
            'message' => 'Mã sự cố không hợp lệ.'
        ];
    }

    // 2. Kiểm tra sự cố có tồn tại không
    $suCo = $this->suCo->LayChiTietSuCo($maSC);

    if (!$suCo) {
        return [
            'success' => false,
            'message' => 'Không tìm thấy sự cố cần xử lý.'
        ];
    }

    // 3. Lấy dữ liệu từ biểu mẫu
    $phuongAn = $duLieu['PhuongAnXuLy'] ?? '';
    $chiPhi = $duLieu['ChiPhiPhatPhatSinh'] ?? '';
    $cuuHo = $duLieu['SuDungCuuHo'] ?? '0';
    $donVi = $duLieu['DonViSuaChua'] ?? '';

    // Chống dữ liệu dạng mảng gây lỗi PHP
    if (
        !is_string($phuongAn) ||
        !is_string($chiPhi) ||
        !is_string($cuuHo) ||
        !is_string($donVi)
    ) {
        return [
            'success' => false,
            'message' => 'Dữ liệu gửi lên không hợp lệ.'
        ];
    }

    $phuongAn = trim($phuongAn);
    $chiPhi = trim($chiPhi);
    $donVi = trim($donVi);

    // 4. Phương án xử lý bắt buộc phải có
    if ($phuongAn === '') {
        return [
            'success' => false,
            'message' => 'Vui lòng nhập phương án xử lý sự cố.'
        ];
    }

    // 5. Kiểm tra chi phí
    if (
        $chiPhi === '' ||
        !is_numeric($chiPhi) ||
        !is_finite((float)$chiPhi) ||
        (float)$chiPhi < 0 ||
        (float)$chiPhi > 9999999999.99
    ) {
        return [
            'success' => false,
            'message' => 'Chi phí phải là số từ 0 đến 9.999.999.999,99 VNĐ.'
        ];
    }

    // 6. Cứu hộ chỉ nhận 0 hoặc 1
    if (!in_array($cuuHo, ['0', '1'], true)) {
        return [
            'success' => false,
            'message' => 'Thông tin sử dụng cứu hộ không hợp lệ.'
        ];
    }

    // 7. Kiểm tra đơn vị sửa chữa
    if (
        mb_strlen($donVi, 'UTF-8') > 150 ||
        mb_strlen($phuongAn, 'UTF-8') > 5000
    ) {
        return [
            'success' => false,
            'message' => 'Nội dung xử lý vượt quá độ dài cho phép.'
        ];
    }

    // 8. Chuẩn bị dữ liệu cập nhật
    $duLieuLuu = [
        'PhuongAnXuLy' => $phuongAn,
        'ChiPhiPhatPhatSinh' => (float)$chiPhi,
        'SuDungCuuHo' => (int)$cuuHo,
        'DonViSuaChua' => $donVi
    ];

    // 9. Gọi Model cập nhật MySQL
    try {
        $thanhCong = $this->suCo->CapNhatXuLySuCo(
            $maSC,
            $duLieuLuu
        );
    } catch (mysqli_sql_exception $e) {
        error_log($e->getMessage());
        $thanhCong = false;
    }

    if (!$thanhCong) {
        return [
            'success' => false,
            'message' => 'Không thể cập nhật kết quả xử lý sự cố.'
        ];
    }

    return [
        'success' => true,
        'message' => 'Cập nhật kết quả xử lý sự cố thành công!'
    ];
}
}
