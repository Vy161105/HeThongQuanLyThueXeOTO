
<?php
if (!function_exists('hSuCo')) {
    function hSuCo($value)
    {
        return htmlspecialchars(
            (string)($value ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
}
?>

<style>
.suco-detail-page {
    max-width: 1000px;
    margin: 40px auto;
    padding: 20px;
    color: #fff;
}

.suco-detail-card {
    padding: 30px;
    background: #1c1c1c;
    border: 1px solid #444;
    border-radius: 10px;
}

.suco-detail-title {
    color: #ef4444;
    margin-bottom: 25px;
}

.suco-detail-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 18px;
}

.suco-detail-item {
    background: #292929;
    border-radius: 6px;
    padding: 18px;
}

.suco-detail-label {
    display: block;
    color: #aaa;
    margin-bottom: 8px;
}

.suco-detail-value {
    font-weight: bold;
    overflow-wrap: anywhere;
}

.suco-detail-section {
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #444;
}

.suco-detail-section h2 {
    font-size: 21px;
}

.suco-detail-text {
    padding: 16px;
    background: #292929;
    border-radius: 6px;
    white-space: pre-wrap;
    overflow-wrap: anywhere;
}

.suco-detail-cost {
    color: #5cdb95;
    font-size: 28px;
    font-weight: bold;
}

.suco-detail-back {
    display: inline-block;
    margin-top: 25px;
    padding: 12px 18px;
    background: #555;
    border-radius: 6px;
    color: #fff;
    text-decoration: none;
}

@media (max-width: 700px) {
    .suco-detail-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="suco-detail-page">

    <?php if (!$chiTietSuCo): ?>

        <div class="suco-detail-card">
            <h1 class="suco-detail-title">
                Không tìm thấy sự cố
            </h1>

            <p>Mã sự cố không tồn tại hoặc không hợp lệ.</p>

            <a class="suco-detail-back"
               href="index.php?page=su-co">
                Quay về danh sách
            </a>
        </div>

    <?php else: ?>

        <div class="suco-detail-card">

            <h1 class="suco-detail-title">
                CHI TIẾT SỰ CỐ #<?= (int)$chiTietSuCo['MaSC'] ?>
            </h1>

            <div class="suco-detail-grid">

                <div class="suco-detail-item">
                    <span class="suco-detail-label">
                        Số hợp đồng
                    </span>
                    <span class="suco-detail-value">
                        <?= hSuCo($chiTietSuCo['SoHD']) ?>
                    </span>
                </div>

                <div class="suco-detail-item">
                    <span class="suco-detail-label">
                        Khách hàng
                    </span>
                    <span class="suco-detail-value">
                        <?= hSuCo($chiTietSuCo['TenKhachHang']) ?>
                    </span>
                </div>

                <div class="suco-detail-item">
                    <span class="suco-detail-label">
                        Loại sự cố
                    </span>
                    <span class="suco-detail-value">
                        <?= hSuCo($chiTietSuCo['LoaiSuCo']) ?>
                    </span>
                </div>

                <div class="suco-detail-item">
                    <span class="suco-detail-label">
                        Thời điểm xảy ra
                    </span>
                    <span class="suco-detail-value">
                        <?= hSuCo($chiTietSuCo['ThoiDiem']) ?>
                    </span>
                </div>

                <div class="suco-detail-item">
                    <span class="suco-detail-label">
                        Vị trí xảy ra
                    </span>
                    <span class="suco-detail-value">
                        <?= hSuCo($chiTietSuCo['ViTri']) ?>
                    </span>
                </div>

                <div class="suco-detail-item">
                    <span class="suco-detail-label">
                        Trạng thái hợp đồng
                    </span>
                    <span class="suco-detail-value">
                        <?= hSuCo($chiTietSuCo['TrangThaiHopDong']) ?>
                    </span>
                </div>

            </div>

            <div class="suco-detail-section">
                <h2>Mô tả sự cố</h2>

                <div class="suco-detail-text"><?= hSuCo(
                    $chiTietSuCo['MoTaSuCo'] ?: 'Chưa có mô tả.'
                ) ?></div>
            </div>

            <div class="suco-detail-section">
                <h2>Chi phí phát sinh</h2>

                <div class="suco-detail-cost">
                    <?= number_format(
                        (float)$chiTietSuCo['ChiPhiPhatPhatSinh'],
                        0,
                        ',',
                        '.'
                    ) ?> VNĐ
                </div>
            </div>

            <div class="suco-detail-section">
                <h2>Phương án xử lý</h2>

                <div class="suco-detail-text"><?= hSuCo(
                    $chiTietSuCo['PhuongAnXuLy']
                        ?: 'Chưa cập nhật phương án xử lý.'
                ) ?></div>
            </div>

            <div class="suco-detail-section">
                <h2>Thông tin cứu hộ và sửa chữa</h2>

                <div class="suco-detail-grid">

                    <div class="suco-detail-item">
                        <span class="suco-detail-label">
                            Sử dụng cứu hộ
                        </span>
                        <span class="suco-detail-value">
                            <?= (int)$chiTietSuCo['SuDungCuuHo'] === 1
                                ? 'Có'
                                : 'Không' ?>
                        </span>
                    </div>

                    <div class="suco-detail-item">
                        <span class="suco-detail-label">
                            Đơn vị sửa chữa
                        </span>
                        <span class="suco-detail-value">
                            <?= hSuCo(
                                $chiTietSuCo['DonViSuaChua']
                                    ?: 'Chưa cập nhật'
                            ) ?>
                        </span>
                    </div>

                </div>
            </div>

            <a class="suco-detail-back"
               href="index.php?page=su-co">
                Quay về danh sách
            </a>

        </div>

    <?php endif; ?>

</main>
