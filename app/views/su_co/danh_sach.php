
<?php
// Hàm hiển thị dữ liệu an toàn
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
.suco-page {
    max-width: 1200px;
    margin: 40px auto;
    padding: 20px;
}

.suco-title {
    margin-bottom: 25px;
    color: #e92730;
}

.suco-table-wrap {
    overflow-x: auto;
}

.suco-table {
    width: 100%;
    border-collapse: collapse;
    background: #1c1c1c;
    color: white;
}

.suco-table th,
.suco-table td {
    padding: 14px;
    border: 1px solid #444;
    text-align: left;
    vertical-align: top;
}

.suco-table th {
    background: #b91c1c;
    color: white;
}

.suco-table tr:hover {
    background: #303030;
}

.suco-link {
    display: inline-block;
    padding: 8px 12px;
    background: #e92730;
    color: white;
    border-radius: 5px;
    text-decoration: none;
}

.suco-empty {
    padding: 25px;
    background: #242424;
    color: white;
    border-radius: 8px;
}

/* Cột thao tác */
.suco-table th:last-child {
    min-width: 205px;
}

.suco-action-cell {
    min-width: 205px;
    vertical-align: middle !important;
}

/* Hai nút nằm ngang */
.suco-actions {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    flex-wrap: nowrap;
}

/* Kiểu nút chung */
.suco-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 80px;
    min-height: 40px;
    padding: 0 12px;
    box-sizing: border-box;
    border-radius: 7px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    white-space: nowrap;
    transition: background-color 0.2s ease,
                transform 0.2s ease;
}

/* Nút Chi tiết */
.suco-action-detail {
    background: #444;
    color: #fff;
}

.suco-action-detail:hover {
    background: #626262;
    transform: translateY(-2px);
}

/* Nút Xử lý */
.suco-action-process {
    background: #dc2626;
    color: #fff;
}

.suco-action-process:hover {
    background: #b91c1c;
    transform: translateY(-2px);
}

/* Khi dùng bàn phím */
.suco-action-btn:focus-visible {
    outline: 2px solid #f5c77a;
    outline-offset: 3px;
}

</style>

<main class="suco-page">

    <h1 class="suco-title">
        QUẢN LÝ SỰ CỐ THUÊ XE
    </h1>

<?php if (!empty($_SESSION['thong_bao_suco'])): ?>
    <div style="
        padding: 15px;
        background: #204b35;
        color: #9af0b9;
        border-radius: 6px;
        margin-bottom: 20px;
    ">
        <?= hSuCo($_SESSION['thong_bao_suco']) ?>
    </div>

    <?php unset($_SESSION['thong_bao_suco']); ?>
<?php endif; ?>

    <div style="margin-bottom: 25px;">
        <a
            href="index.php?page=them-su-co"
            class="suco-link"
        >
            + Ghi nhận sự cố mới
        </a>
    </div>

    <p>Danh sách các sự cố phát sinh trong quá trình thuê xe.</p>

    <?php if ($danhSachSuCo === false): ?>

        <div class="suco-empty">
            Không thể tải danh sách sự cố.
            Vui lòng kiểm tra kết nối và cấu trúc database.
        </div>

    <?php elseif (empty($danhSachSuCo)): ?>

        <div class="suco-empty">
            Chưa có sự cố nào được ghi nhận.
        </div>

    <?php else: ?>

        <div class="suco-table-wrap">
            <table class="suco-table">

                <thead>
                    <tr>
                        <th>Mã sự cố</th>
                        <th>Số hợp đồng</th>
                        <th>Khách hàng</th>
                        <th>Loại sự cố</th>
                        <th>Vị trí</th>
                        <th>Thời điểm</th>
                        <th>Chi phí</th>
                        <th>Phương án xử lý</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($danhSachSuCo as $suCo): ?>
                        <tr>
                            <td>
                                <?= hSuCo($suCo['MaSC']) ?>
                            </td>

                            <td>
                                <?= hSuCo($suCo['SoHD']) ?>
                            </td>

                            <td>
                                <?= hSuCo($suCo['TenKhachHang']) ?>
                            </td>

                            <td>
                                <?= hSuCo($suCo['LoaiSuCo']) ?>
                            </td>

                            <td>
                                <?= hSuCo($suCo['ViTri']) ?>
                            </td>

                            <td>
                                <?= hSuCo($suCo['ThoiDiem']) ?>
                            </td>

                            <td>
                                <?= number_format(
                                    (float)$suCo['ChiPhiPhatPhatSinh'],
                                    0,
                                    ',',
                                    '.'
                                ) ?> VNĐ
                            </td>

                            <td>
                                <?= hSuCo(
                                    $suCo['PhuongAnXuLy']
                                    ?: 'Chưa cập nhật'
                                ) ?>
                            </td>                                                       
                            
                            <td class="suco-action-cell">
                                <div class="suco-actions">

                                    <a
                                        href="index.php?page=chi-tiet-su-co&amp;id=<?= (int)$suCo['MaSC'] ?>"
                                        class="suco-action-btn suco-action-detail"
                                    >
                                        Chi tiết
                                    </a>

                                    <a
                                        href="index.php?page=xu-ly-su-co&amp;id=<?= (int)$suCo['MaSC'] ?>"
                                        class="suco-action-btn suco-action-process"
                                    >
                                        Xử lý
                                    </a>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>

    <?php endif; ?>

</main>
