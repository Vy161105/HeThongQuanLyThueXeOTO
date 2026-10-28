
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
.suco-process-page {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    color: #fff;
}

.suco-process-card {
    background: #1c1c1c;
    border: 1px solid #444;
    border-radius: 10px;
    padding: 30px;
}

.suco-process-title {
    color: #ef4444;
    margin: 0 0 25px;
}

.suco-process-info {
    background: #292929;
    border-radius: 6px;
    padding: 18px;
    margin-bottom: 25px;
    line-height: 1.8;
}

.suco-process-group {
    margin-bottom: 20px;
}

.suco-process-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 9px;
}

.suco-process-group input,
.suco-process-group select,
.suco-process-group textarea {
    width: 100%;
    padding: 12px;
    box-sizing: border-box;
    background: #292929;
    border: 1px solid #555;
    border-radius: 6px;
    color: #fff;
    font: inherit;
}

.suco-process-group textarea {
    min-height: 120px;
    resize: vertical;
}

.suco-process-error {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    background: #3d301d;
    color: #fcd58d;
}

.suco-process-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 25px;
}

.suco-process-btn {
    display: inline-block;
    padding: 12px 18px;
    border: 0;
    border-radius: 6px;
    background: #e92730;
    color: #fff;
    font: inherit;
    text-decoration: none;
    cursor: pointer;
}

.suco-process-btn-secondary {
    background: #555;
}
</style>

<main class="suco-process-page">

    <div class="suco-process-card">

        <?php if (!$chiTietSuCo): ?>

            <h1 class="suco-process-title">
                Không tìm thấy sự cố
            </h1>

            <p>Mã sự cố không tồn tại hoặc không hợp lệ.</p>

            <a
                href="index.php?page=su-co"
                class="suco-process-btn suco-process-btn-secondary"
            >
                Quay về danh sách
            </a>

        <?php else: ?>

            <h1 class="suco-process-title">
                XỬ LÝ SỰ CỐ #<?= (int)$chiTietSuCo['MaSC'] ?>
            </h1>

            <div class="suco-process-info">
                <div>
                    <strong>Hợp đồng:</strong>
                    <?= hSuCo($chiTietSuCo['SoHD']) ?>
                </div>

                <div>
                    <strong>Khách hàng:</strong>
                    <?= hSuCo($chiTietSuCo['TenKhachHang']) ?>
                </div>

                <div>
                    <strong>Loại sự cố:</strong>
                    <?= hSuCo($chiTietSuCo['LoaiSuCo']) ?>
                </div>

                <div>
                    <strong>Thời điểm:</strong>
                    <?= hSuCo($chiTietSuCo['ThoiDiem']) ?>
                </div>

                <div>
                    <strong>Mô tả:</strong>
                    <?= hSuCo($chiTietSuCo['MoTaSuCo']) ?>
                </div>
            </div>

            <?php if ($loiXuLy !== ''): ?>
                <div class="suco-process-error" role="alert">
                    <?= hSuCo($loiXuLy) ?>
                </div>
            <?php endif; ?>

            <form
                method="POST"
                action="index.php?page=xu-ly-su-co&amp;id=<?= (int)$chiTietSuCo['MaSC'] ?>"
            >

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= hSuCo($_SESSION['csrf_suco']) ?>"
                >

                <div class="suco-process-group">
                    <label for="PhuongAnXuLy">
                        Phương án xử lý *
                    </label>

                    <textarea
                        id="PhuongAnXuLy"
                        name="PhuongAnXuLy"
                        maxlength="5000"
                        placeholder="Nhập phương án xử lý sự cố..."
                        required
                    ><?= hSuCo($duLieuXuLy['PhuongAnXuLy'] ?? '') ?></textarea>
                </div>

                <div class="suco-process-group">
                    <label for="ChiPhiPhatPhatSinh">
                        Chi phí phát sinh (VNĐ) *
                    </label>

                    <input
                        type="number"
                        id="ChiPhiPhatPhatSinh"
                        name="ChiPhiPhatPhatSinh"
                        min="0"
                        max="9999999999.99"
                        step="0.01"
                        value="<?= hSuCo($duLieuXuLy['ChiPhiPhatPhatSinh'] ?? '0') ?>"
                        required
                    >
                </div>

                <div class="suco-process-group">
                    <label for="SuDungCuuHo">
                        Sử dụng cứu hộ
                    </label>

                    <select
                        id="SuDungCuuHo"
                        name="SuDungCuuHo"
                    >
                        <option
                            value="0"
                            <?= (string)($duLieuXuLy['SuDungCuuHo'] ?? '0') === '0'
                                ? 'selected'
                                : '' ?>
                        >
                            Không
                        </option>

                        <option
                            value="1"
                            <?= (string)($duLieuXuLy['SuDungCuuHo'] ?? '0') === '1'
                                ? 'selected'
                                : '' ?>
                        >
                            Có
                        </option>
                    </select>
                </div>

                <div class="suco-process-group">
                    <label for="DonViSuaChua">
                        Đơn vị sửa chữa
                    </label>

                    <input
                        type="text"
                        id="DonViSuaChua"
                        name="DonViSuaChua"
                        maxlength="150"
                        value="<?= hSuCo($duLieuXuLy['DonViSuaChua'] ?? '') ?>"
                        placeholder="Ví dụ: Gara AUK"
                    >
                </div>

                <div class="suco-process-actions">

                    <button
                        type="submit"
                        class="suco-process-btn"
                    >
                        Cập nhật kết quả
                    </button>

                    <a
                        href="index.php?page=chi-tiet-su-co&amp;id=<?= (int)$chiTietSuCo['MaSC'] ?>"
                        class="suco-process-btn suco-process-btn-secondary"
                    >
                        Hủy / Xem chi tiết
                    </a>

                </div>

            </form>

        <?php endif; ?>

    </div>

</main>
