<?php
session_start();

$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'home':
        $pageTitle = 'Trang chủ';
        $view = __DIR__ . '/app/views/home/trang_chu.php';
        break;

    case 'dang-ky':
        $pageTitle = 'Đăng ký khách hàng';

        require_once __DIR__ . '/app/controllers/cDangKy.php';

        $thongBao = '';
        $thanhCong = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller = new cDangKy();
            $ketQua = $controller->DangKy($_POST);

            $thongBao = $ketQua['message'] ?? '';
            $thanhCong = $ketQua['success'] ?? false;
        }

        $view = __DIR__ . '/app/views/auth/dang_ky.php';
        break;

    case 'mau-xe':
        $pageTitle = 'Danh mục xe';

        require_once __DIR__ . '/app/controllers/cDatXe.php';

        $controller = new cDatXe();
        $dsMauXe = $controller->LayDanhSachMauXe();

        $view = __DIR__ . '/app/views/dat_xe/danh_sach_mau_xe.php';
        break;

    case 'dat-xe':
        // UC08 - Đặt thuê xe online
        $pageTitle = 'Đặt thuê xe online';

        require_once __DIR__ . '/app/controllers/cDatXe.php';

        $controller = new cDatXe();
        $maKH = $_SESSION['MaKH'] ?? null;
        $maMauXe = trim($_GET['MaMauXe'] ?? '');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ketQua = $controller->DatXe($maKH, $maMauXe, $_POST);
            $bieuMau = $controller->LayBieuMau($maKH, $maMauXe);
        } else {
            $ketQua = $controller->LayBieuMau($maKH, $maMauXe);
            $bieuMau = $ketQua;
        }

        $khachHang = $bieuMau['khachHang'] ?? null;
        $mauXe = $bieuMau['mauXe'] ?? null;
        $thongBao = $ketQua['message'] ?? '';

        // Mở biểu mẫu thành công (GET) chưa phải là đã đặt xong.
        $thanhCong = $_SERVER['REQUEST_METHOD'] === 'POST'
            && ($ketQua['success'] ?? false);

        $view = __DIR__ . '/app/views/dat_xe/dat_xe.php';
        break;

    case 'demo-dang-nhap':
        // CHỈ DÙNG ĐỂ DEMO UC08 khi chức năng đăng nhập thật chưa xong.
        // Xóa route này khi đã có đăng nhập thật (nhớ set $_SESSION['MaKH']).
        $_SESSION['MaKH'] = 'KH01';
        header('Location: index.php?page=mau-xe');
        exit;

    case 'demo-dang-xuat':
        unset($_SESSION['MaKH']);
        header('Location: index.php?page=mau-xe');
        exit;

    case 'dang-nhap':
        http_response_code(404);
        $pageTitle = 'Đăng nhập';
        echo 'Chức năng đăng nhập chưa được xây dựng.';
        exit;

    default:
        http_response_code(404);
        $pageTitle = 'Không tìm thấy trang';
        echo 'Trang bạn yêu cầu không tồn tại.';
        exit;
}

require __DIR__ . '/app/views/layouts/dau_trang.php';
require $view;
require __DIR__ . '/app/views/layouts/chan_trang.php';
