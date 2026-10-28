
<?php
// Khởi động Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Xác định trang người dùng yêu cầu
$page = $_GET['page'] ?? 'home';

// Không chấp nhận page dạng mảng
if (!is_string($page)) {
    http_response_code(400);
    exit('Đường dẫn không hợp lệ.');
}

/*
 * PHÂN QUYỀN UC18
 *
 * Khi thành viên phụ trách đăng nhập hoàn thành,
 * cần kiểm tra Session và quyền nhân viên tại đây.
 *
 * Hiện chưa kích hoạt vì hệ thống đăng nhập chung
 * chưa được xây dựng.
 *
 * Các trang cần bảo vệ:
 * - su-co
 * - them-su-co
 * - chi-tiet-su-co
 * - xu-ly-su-co
 */

switch ($page) {

    // ===================================
    // TRANG CHỦ
    // ===================================
    case 'home':
        $pageTitle = 'Trang chủ';

        $view = __DIR__ .
            '/app/views/home/trang_chu.php';
        break;


    // ===================================
    // ĐĂNG KÝ KHÁCH HÀNG - CODE CỦA NHÓM
    // ===================================
    case 'dang-ky':
        $pageTitle = 'Đăng ký khách hàng';

        require_once __DIR__ .
            '/app/controllers/cDangKy.php';

        $thongBao = '';
        $thanhCong = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new cDangKy();

            $ketQua = $controller->DangKy($_POST);

            $thongBao = $ketQua['message'] ?? '';
            $thanhCong = $ketQua['success'] ?? false;
        }

        $view = __DIR__ .
            '/app/views/auth/dang_ky.php';
        break;


    // ===================================
    // DANH SÁCH SỰ CỐ - UC18
    // ===================================
    case 'su-co':
        $pageTitle = 'Danh sách sự cố thuê xe';

        require_once __DIR__ .
            '/app/controllers/cSuCo.php';

        $controller = new cSuCo();

        $danhSachSuCo = $controller->DanhSachSuCo();

        $view = __DIR__ .
            '/app/views/su_co/danh_sach.php';
        break;


    // ===================================
    // GHI NHẬN SỰ CỐ MỚI - UC18
    // ===================================
    case 'them-su-co':
        $pageTitle = 'Ghi nhận sự cố thuê xe';

        require_once __DIR__ .
            '/app/controllers/cSuCo.php';

        $controller = new cSuCo();

        $danhSachHopDong = $controller->HopDongDangThue();

        // Khởi tạo CSRF token
        if (empty($_SESSION['csrf_suco'])) {
            $_SESSION['csrf_suco'] =
                bin2hex(random_bytes(32));
        }

        $loiSuCo = '';

        // Giá trị mặc định của biểu mẫu
        $duLieuCu = [
            'MaHD' => '',
            'LoaiSuCo' => '',
            'MoTaSuCo' => '',
            'ViTri' => '',
            'ThoiDiem' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Giữ dữ liệu khi biểu mẫu có lỗi
            foreach ($duLieuCu as $tenTruong => $giaTri) {
                $giaTriGuiLen = $_POST[$tenTruong] ?? '';

                $duLieuCu[$tenTruong] =
                    is_string($giaTriGuiLen)
                    ? $giaTriGuiLen
                    : '';
            }

            // Kiểm tra CSRF
            $tokenGuiLen = $_POST['csrf_token'] ?? '';

            if (
                !is_string($tokenGuiLen) ||
                !hash_equals(
                    $_SESSION['csrf_suco'],
                    $tokenGuiLen
                )
            ) {
                http_response_code(403);

                $loiSuCo =
                    'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.';

            } elseif ($danhSachHopDong === false) {

                $loiSuCo =
                    'Không thể kiểm tra danh sách hợp đồng.';

            } else {

                // Gọi Controller ghi nhận sự cố
                $ketQua = $controller->GhiNhanSuCo($_POST);

                if ($ketQua['success']) {

                    $_SESSION['thong_bao_suco'] =
                        'Ghi nhận sự cố thành công! Mã sự cố: ' .
                        $ketQua['MaSC'];

                    // Đổi token sau khi lưu thành công
                    $_SESSION['csrf_suco'] =
                        bin2hex(random_bytes(32));

                    // Chuyển về danh sách
                    header(
                        'Location: index.php?page=su-co',
                        true,
                        303
                    );
                    exit;
                }

                $loiSuCo = $ketQua['message'];
            }
        }

        $view = __DIR__ .
            '/app/views/su_co/them.php';
        break;


    // ===================================
    // CHI TIẾT SỰ CỐ - UC18
    // ===================================
    case 'chi-tiet-su-co':
        $pageTitle = 'Chi tiết sự cố thuê xe';

        require_once __DIR__ .
            '/app/controllers/cSuCo.php';

        $controller = new cSuCo();

        $maSC = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        $chiTietSuCo = $controller->ChiTietSuCo($maSC);

        if (!$chiTietSuCo) {
            http_response_code(404);
        }

        $view = __DIR__ .
            '/app/views/su_co/chi_tiet.php';
        break;


    // ===================================
    // XỬ LÝ SỰ CỐ - UC18
    // ===================================
    case 'xu-ly-su-co':
        $pageTitle = 'Xử lý sự cố thuê xe';

        require_once __DIR__ .
            '/app/controllers/cSuCo.php';

        $controller = new cSuCo();

        $maSC = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        $chiTietSuCo = $controller->ChiTietSuCo($maSC);

        $loiXuLy = '';

        // Giá trị đang lưu trong database
        $duLieuXuLy = $chiTietSuCo ?: [];

        if (!$chiTietSuCo) {

            http_response_code(404);

        } else {

            // Khởi tạo CSRF token
            if (empty($_SESSION['csrf_suco'])) {
                $_SESSION['csrf_suco'] =
                    bin2hex(random_bytes(32));
            }

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {

                // Giữ lại dữ liệu vừa nhập
                foreach (
                    [
                        'PhuongAnXuLy',
                        'ChiPhiPhatPhatSinh',
                        'SuDungCuuHo',
                        'DonViSuaChua'
                    ] as $tenTruong
                ) {
                    $giaTri = $_POST[$tenTruong] ?? '';

                    $duLieuXuLy[$tenTruong] =
                        is_string($giaTri)
                        ? $giaTri
                        : '';
                }

                // Kiểm tra CSRF
                $tokenGuiLen = $_POST['csrf_token'] ?? '';

                if (
                    !is_string($tokenGuiLen) ||
                    !hash_equals(
                        $_SESSION['csrf_suco'],
                        $tokenGuiLen
                    )
                ) {
                    http_response_code(403);

                    $loiXuLy =
                        'Phiên biểu mẫu không hợp lệ. Vui lòng tải lại trang.';

                } else {

                    // Gọi Controller cập nhật xử lý
                    $ketQua = $controller->XuLySuCo(
                        $maSC,
                        $duLieuXuLy
                    );

                    if ($ketQua['success']) {

                        $_SESSION['thong_bao_suco'] =
                            'Cập nhật kết quả xử lý sự cố #' .
                            $maSC .
                            ' thành công!';

                        // Đổi token sau khi cập nhật
                        $_SESSION['csrf_suco'] =
                            bin2hex(random_bytes(32));

                        header(
                            'Location: index.php?page=su-co',
                            true,
                            303
                        );
                        exit;
                    }

                    $loiXuLy = $ketQua['message'];
                }
            }
        }

        $view = __DIR__ .
            '/app/views/su_co/xu_ly.php';
        break;


    // ===================================
    // ĐĂNG NHẬP - CHỜ THÀNH VIÊN NHÓM
    // ===================================
    case 'dang-nhap':
        http_response_code(404);

        $pageTitle = 'Đăng nhập';

        echo 'Chức năng đăng nhập chưa được xây dựng.';
        exit;


    // ===================================
    // TRANG KHÔNG TỒN TẠI
    // ===================================
    default:
        http_response_code(404);

        $pageTitle = 'Không tìm thấy trang';

        echo 'Trang bạn yêu cầu không tồn tại.';
        exit;
}


// =======================================
// GIAO DIỆN CHUNG CỦA NHÓM
// =======================================
require __DIR__ .
    '/app/views/layouts/dau_trang.php';

require $view;

require __DIR__ .
    '/app/views/layouts/chan_trang.php';
