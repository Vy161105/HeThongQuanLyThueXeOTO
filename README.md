# HeThongQuanLyThueXeOTO

Bản tích hợp: Đăng ký khách hàng + UC08 Đặt thuê xe online (chung một `index.php`).

## Cài đặt
1. Import theo thứ tự: `database/00_danh_muc_xe.sql` → `database/oto_database.sql` → `database/02_du_lieu_demo.sql`.
2. Chỉnh `config/Connect.php` nếu MySQL không dùng `root` / mật khẩu rỗng.
3. Chạy `php -S localhost:8000` trong thư mục này, mở `http://localhost:8000/index.php`.

## Route (`index.php?page=...`)
- `home`, `dang-ky` — trang chủ, đăng ký khách hàng
- `mau-xe`, `dat-xe&MaMauXe=...` — danh mục xe, đặt thuê xe (UC08, cần `$_SESSION['MaKH']`)
- `demo-dang-nhap` / `demo-dang-xuat` — chỉ để demo UC08 (đăng nhập KH01); xóa khi có đăng nhập thật
- `dang-nhap` — chưa xây dựng
