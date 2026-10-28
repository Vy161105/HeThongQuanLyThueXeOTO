<?php
$e = static function ($v) {
    return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8');
};
$tien = static function ($v) {
    return number_format((float) $v, 0, ',', '.') . 'đ';
};
$ma = $ketQua['code'] ?? '';
?>
<main class="auth-bg">
    <div class="wrap">
        <section class="register-card">
            <div class="eyebrow">ĐẶT THUÊ XE ONLINE</div>
            <h1>Đặt thuê xe</h1>

            <?php if (!empty($thongBao)): ?>
                <p class="form-message" role="status"><?= $e($thongBao) ?></p>
            <?php endif; ?>

            <?php if ($thanhCong): ?>
                <div class="auth-bottom">
                    Mã đơn của bạn: <strong><?= $e($ketQua['maDD']) ?></strong><br>
                    Mẫu xe: <?= $e($mauXe['TenMauXe']) ?> · <?= (int) $ketQua['soNgay'] ?> ngày ·
                    Tổng giá dự tính: <strong><?= $tien($ketQua['tongGia']) ?></strong><br>
                    Trạng thái: Chờ xác nhận.
                </div>
                <div class="auth-bottom">
                    <a href="index.php?page=mau-xe">Quay về danh mục xe</a>
                </div>

            <?php elseif ($ma === 'HET_PHIEN'): ?>
                <div class="auth-bottom">
                    <a href="index.php?page=demo-dang-nhap">Đăng nhập demo (KH01)</a> ·
                    <a href="index.php?page=dang-nhap">Đăng nhập</a>
                </div>

            <?php elseif ($ma === 'MAU_XE' || empty($mauXe)): ?>
                <div class="auth-bottom">
                    <a href="index.php?page=mau-xe">Chọn lại mẫu xe</a>
                </div>

            <?php elseif ($ma === 'GPLX'): ?>
                <div class="auth-bottom">
                    Cần cập nhật hồ sơ khách hàng (UC03) trước khi đặt xe.
                    <a href="index.php?page=mau-xe">Quay về danh mục xe</a>
                </div>

            <?php elseif ($ma === 'TRUNG_DON'): ?>
                <div class="auth-bottom">
                    Đơn hiện có: <strong><?= $e($ketQua['donTrung']['MaDD']) ?></strong> ·
                    <?= $e($ketQua['donTrung']['NgayNhan']) ?> → <?= $e($ketQua['donTrung']['NgayTra']) ?> ·
                    <?= $tien($ketQua['donTrung']['TongGia']) ?> ·
                    <?= $e($ketQua['donTrung']['TrangThai']) ?>
                </div>
                <div class="auth-bottom">
                    <a href="index.php?page=mau-xe">Quay về danh mục xe</a>
                </div>

            <?php else: ?>
                <p>Kiểm tra thông tin và chọn ngày nhận, ngày trả xe.</p>

                <form class="register-grid" method="POST"
                      action="index.php?page=dat-xe&amp;MaMauXe=<?= urlencode($mauXe['MaMauXe']) ?>"
                      autocomplete="off">

                    <label class="field">
                        Mẫu xe
                        <input value="<?= $e($mauXe['TenMauXe']) ?>" readonly>
                    </label>

                    <label class="field">
                        Giá thuê / ngày
                        <input value="<?= $tien($mauXe['Gia']) ?>" readonly>
                    </label>

                    <label class="field wide">
                        Họ và tên
                        <input value="<?= $e($khachHang['HoVaTen']) ?>" readonly>
                    </label>

                    <label class="field">
                        Số điện thoại
                        <input value="<?= $e($khachHang['DienThoai']) ?>" readonly>
                    </label>

                    <label class="field">
                        Số giấy phép lái xe
                        <input value="<?= $e($khachHang['GiayPhepLaiXe']) ?>" readonly>
                    </label>

                    <label class="field">
                        Ngày nhận xe <em>*</em>
                        <input name="NgayNhan" type="date" min="<?= date('Y-m-d') ?>"
                               value="<?= $e($_POST['NgayNhan'] ?? '') ?>">
                    </label>

                    <label class="field">
                        Ngày trả xe <em>*</em>
                        <input name="NgayTra" type="date" min="<?= date('Y-m-d') ?>"
                               value="<?= $e($_POST['NgayTra'] ?? '') ?>">
                    </label>

                    <div class="register-actions wide">
                        <a href="index.php?page=mau-xe" class="button button-muted">HỦY</a>
                        <button class="button button-red" type="submit">XÁC NHẬN ĐẶT THUÊ →</button>
                    </div>
                </form>
            <?php endif; ?>
        </section>
    </div>
</main>
