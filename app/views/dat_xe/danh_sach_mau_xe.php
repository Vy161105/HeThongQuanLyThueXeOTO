<main>
    <section class="section">
        <div class="wrap">
            <div class="section-heading">
                <span class="eyebrow">DANH MỤC XE</span>
                <h2>Chọn mẫu xe cần thuê</h2>
                <p>Nhấn Đặt để mở biểu mẫu đặt thuê xe online (UC08).</p>
            </div>

            <?php if (!empty($_SESSION['MaKH'])): ?>
                <p class="demo-note">
                    Đang đăng nhập (demo) với mã khách hàng
                    <?= htmlspecialchars($_SESSION['MaKH'], ENT_QUOTES, 'UTF-8') ?> ·
                    <a href="index.php?page=demo-dang-xuat">Đăng xuất demo</a>
                </p>
            <?php else: ?>
                <p class="demo-note">
                    Bạn chưa đăng nhập. Chức năng đăng nhập chưa hoàn thành, dùng
                    <a href="index.php?page=demo-dang-nhap">đăng nhập demo (KH01)</a>.
                </p>
            <?php endif; ?>

            <div class="category-grid">
                <?php foreach ($dsMauXe as $mauXe): ?>
                    <article class="category">
                        <div class="category-art">🚗</div>
                        <h3><?= htmlspecialchars($mauXe['TenMauXe'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p>
                            <?= htmlspecialchars($mauXe['TenPhanKhuc'] ?? '', ENT_QUOTES, 'UTF-8') ?><br>
                            <strong><?= number_format((float) $mauXe['Gia'], 0, ',', '.') ?>đ / ngày</strong>
                        </p>
                        <a class="button button-red"
                           href="index.php?page=dat-xe&amp;MaMauXe=<?= urlencode($mauXe['MaMauXe']) ?>">ĐẶT →</a>
                    </article>
                <?php endforeach; ?>

                <?php if (empty($dsMauXe)): ?>
                    <p>Chưa có mẫu xe nào trong hệ thống.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>
