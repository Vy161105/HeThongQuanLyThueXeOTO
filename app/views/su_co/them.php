
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
.suco-form-page {
    max-width: 900px;
    margin: 40px auto;
    padding: 20px;
    color: #fff;
}

.suco-form-card {
    background: #1c1c1c;
    border: 1px solid #444;
    border-radius: 10px;
    padding: 30px;
}

.suco-form-title {
    color: #ef4444;
    margin: 0 0 25px;
}

.suco-form-group {
    margin-bottom: 20px;
}

.suco-form-group label {
    display: block;
    margin-bottom: 9px;
    font-weight: bold;
}

.suco-form-group input,
.suco-form-group select,
.suco-form-group textarea {
    width: 100%;
    padding: 12px;
    box-sizing: border-box;
    background: #292929;
    border: 1px solid #555;
    border-radius: 6px;
    color: white;
    font: inherit;
}

.suco-form-group textarea {
    min-height: 120px;
    resize: vertical;
}

.suco-form-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 25px;
}

.suco-form-btn {
    display: inline-block;
    background: #e92730;
    padding: 12px 20px;
    border: none;
    border-radius: 6px;
    color: white;
    text-decoration: none;
    font: inherit;
    cursor: pointer;
}

.suco-form-btn-secondary {
    background: #555;
}

.suco-form-btn:disabled {
    background: #666;
    cursor: not-allowed;
}

.suco-form-notice {
    padding: 15px;
    margin-bottom: 20px;
    border-radius: 6px;
    background: #3d301d;
    color: #fcd58d;
}
</style>

<main class="suco-form-page">

    <div class="suco-form-card">

        <h1 class="suco-form-title">
            GHI NHẬN SỰ CỐ THUÊ XE
        </h1>
        <?php if (!empty($loiSuCo)): ?>
    <div class="suco-form-notice" role="alert">
        <?= hSuCo($loiSuCo) ?>
    </div>
<?php endif; ?>
        <?php if ($danhSachHopDong === false): ?>

            <div class="suco-form-notice">
                Không thể tải danh sách hợp đồng.
                Vui lòng kiểm tra kết nối database.
            </div>

        <?php elseif (empty($danhSachHopDong)): ?>

            <div class="suco-form-notice">
                Hiện chưa có hợp đồng thuê xe đang có hiệu lực.
                Không thể ghi nhận sự cố mới.
            </div>

        <?php endif; ?>

        <form method="POST" action="index.php?page=them-su-co">
            <input
    type="hidden"
    name="csrf_token"
    value="<?= hSuCo($_SESSION['csrf_suco']) ?>"
>
            <div class="suco-form-group">
                <label for="MaHD">Hợp đồng thuê xe *</label>

                <select
                    id="MaHD"
                    name="MaHD"
                    required
                    <?= empty($danhSachHopDong) ? 'disabled' : '' ?>
                >
                    <option value="">
                        -- Chọn hợp đồng đang thuê --
                    </option>

                    <?php foreach (($danhSachHopDong ?: []) as $hd): ?>
                        
<option
    value="<?= (int)$hd['MaHD'] ?>"
    <?= (string)($duLieuCu['MaHD'] ?? '') === (string)$hd['MaHD']
        ? 'selected'
        : '' ?>
>

                            <?= hSuCo($hd['SoHD']) ?>
                            -
                            <?= hSuCo($hd['TenKhachHang']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>
            </div>

            <div class="suco-form-group">
                <label for="LoaiSuCo">Loại sự cố *</label>

                <select id="LoaiSuCo" name="LoaiSuCo" required>
                    
<option value="">-- Chọn loại sự cố --</option>

<?php
$dsLoaiSuCo = [
    'Mat chia khoa' => 'Mất chìa khóa',
    'Va cham nhe' => 'Va chạm nhẹ',
    'Tai nan nghiem trong' => 'Tai nạn nghiêm trọng',
    'Khac' => 'Khác'
];
?>

<?php foreach ($dsLoaiSuCo as $giaTri => $tenLoai): ?>
    <option
        value="<?= hSuCo($giaTri) ?>"
        <?= ($duLieuCu['LoaiSuCo'] ?? '') === $giaTri
            ? 'selected'
            : '' ?>
    >
        <?= hSuCo($tenLoai) ?>
    </option>
<?php endforeach; ?>

                </select>
            </div>

            <div class="suco-form-group">
                <label for="MoTaSuCo">Mô tả sự cố *</label>

                
<textarea
    id="MoTaSuCo"
    name="MoTaSuCo"
    maxlength="5000"
    placeholder="Nhập mô tả sự cố..."
    required
><?= hSuCo($duLieuCu['MoTaSuCo'] ?? '') ?></textarea>

            </div>

            <div class="suco-form-group">
                <label for="ViTri">Vị trí xảy ra *</label>

                
                <input
                    type="text"
                    id="ViTri"
                    name="ViTri"
                    minlength="10"
                    maxlength="255"
                    value="<?= hSuCo($duLieuCu['ViTri'] ?? '') ?>"
                    placeholder="Nhập địa điểm xảy ra sự cố"
                    required
                >

                <small style="display:block; color:#aaa; margin-top:6px;">
                    Nhập tên đường, khu vực hoặc địa điểm xảy ra sự cố.
                </small>

            </div>

            <div class="suco-form-group">
                <label for="ThoiDiem">Thời điểm xảy ra *</label>

                <input
                    type="datetime-local"
                    id="ThoiDiem"
                    name="ThoiDiem"                    
                    value="<?= hSuCo($duLieuCu['ThoiDiem'] ?? '') ?>"
                    required
                >
            </div>

            <div class="suco-form-actions">

                
<button
    type="submit"
    class="suco-form-btn"
    <?= empty($danhSachHopDong) ? 'disabled' : '' ?>
>
    Lưu sự cố
</button>


                <a
                    href="index.php?page=su-co"
                    class="suco-form-btn suco-form-btn-secondary"
                >
                    Quay về danh sách
                </a>

            </div>

        </form>

    </div>

</main>
