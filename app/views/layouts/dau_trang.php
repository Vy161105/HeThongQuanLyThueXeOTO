
<?php
$pageTitle = $pageTitle ?? 'Trang chủ';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php');

if (!function_exists('activeNav')) {
    function activeNav($file)
    {
        global $currentPage;
        return $currentPage === $file ? ' active' : '';
    }
}
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="AUK Car Rental - hệ thống quản lý thuê xe ô tô">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | AUK Car Rental</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="topbar">
    <div class="wrap topbar-inner">
        <span>⌖ &nbsp;HỆ THỐNG THUÊ XE Ô TÔ AUK</span>
        <span>Hỗ trợ khách hàng &amp; nhân viên</span>
    </div>
</div>

<header class="brand-header">
    <div class="wrap brand-row">
        <a href="index.php" class="brand">
            <span class="brand-symbol">✺</span>
            <span>AUK <b>CAR RENTAL</b>
                <small>ĐỒNG HÀNH TRÊN MỌI HÀNH TRÌNH</small>
            </span>
        </a>
        <div class="brand-center">
            <strong>THUÊ XE TỰ LÁI · ĐẶT XE THUẬN TIỆN</strong>
            <span>Giao diện mô phỏng hệ thống quản lý thuê xe</span>
        </div>
        <div class="brand-icon" aria-hidden="true">🚘</div>
    </div>
</header>

<nav class="main-nav" aria-label="Điều hướng chính">
    <div class="wrap nav-row">
        <button class="menu-toggle" type="button"
                aria-expanded="false" aria-controls="nav-items">
            ☰ &nbsp; DANH MỤC
        </button>
        <div class="nav-items" id="nav-items">
            <a class="<?= trim(activeNav('index.php')) ?>" href="index.php">TRANG CHỦ</a>
            <a href="index.php#gioi-thieu">GIỚI THIỆU</a>
            <a href="index.php?page=mau-xe">DANH MỤC XE</a>
            <a href="index.php#quy-trinh">QUY TRÌNH THUÊ XE</a>
            <a href="index.php#lien-he">LIÊN HỆ</a>
        </div>
        <div class="nav-auth">
            <a class="<?= trim(($_GET['page'] ?? '') === 'dang-nhap' ? 'active' : '') ?>" href="index.php?page=dang-nhap">ĐĂNG NHẬP</a>
            <a class="nav-register<?= ($_GET['page'] ?? '') === 'dang-ky' ? ' active' : '' ?>" href="index.php?page=dang-ky">ĐĂNG KÝ</a>
        </div>
    </div>
</nav>
