<?php
/**
 * Partial Header - Thanh điều hướng chính (navbar) cho toàn bộ trang client.
 * Chứa: logo, menu chính (Trang chủ, Tra cứu, Tin tức, Biển báo, Bản đồ, Thống kê, FAQ),
 *        khu vực đăng nhập/đăng ký hoặc dropdown tài khoản người dùng.
 * Được nhúng vào client.php layout.
 */
/* Import Session để kiểm tra trạng thái đăng nhập và lấy thông tin người dùng */
use App\Core\Session;
/* Xác định người dùng đã đăng nhập hay chưa để hiển thị giao diện tương ứng */
$isLoggedIn = Session::isLoggedIn();
/* Kiểm tra quyền admin để hiển thị link vào admin panel */
$isAdmin = Session::isAdmin();
/* Lấy URL hiện tại để đánh dấu menu đang active.
   parse_url(..., PHP_URL_PATH): chỉ lấy phần đường dẫn, bỏ qua query string */
$currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
/**
 * Hàm helper kiểm tra một đường dẫn có khớp chính xác URL hiện tại không.
 * Dùng cho các menu có URL cố định (Trang chủ).
 * Trả về CSS class 'active' để Bootstrap tô đậm mục đang chọn.
 */
function isActive(string $path): string {
    global $currentUrl; // Truy cập biến toàn cục $currentUrl
    return $currentUrl === $path ? 'active' : '';
}
?>
<!-- navbar: thành phần thanh điều hướng của Bootstrap.
     navbar-expand-lg: trên màn hình ≥992px (large) thì menu mở rộng ngang; dưới thì thu gọn thành hamburger.
     navbar-dark: chữ màu sáng (trắng) trên nền tối.
     bg-primary: nền màu xanh dương chủ đạo của Bootstrap.
     sticky-top: thanh điều hướng dính vào đỉnh màn hình khi cuộn trang. -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <!-- container: căn giữa nội dung và giới hạn chiều rộng theo breakpoint -->
    <div class="container">
        <!-- navbar-brand: logo/thương hiệu, thường là link về trang chủ.
             fw-bold: font-weight bold (chữ đậm) -->
        <a class="navbar-brand fw-bold" href="/">
            <!-- fa-car: icon xe hơi, liên quan đến chủ đề giao thông. me-2: margin-right 0.5rem -->
            <i class="fas fa-car me-2"></i>Tra Cứu Phạt Nguội
        </a>
        <!-- navbar-toggler: nút hamburger (3 gạch) hiển thị trên mobile khi menu bị thu gọn.
             data-bs-toggle="collapse": kích hoạt collapse.
             data-bs-target="#mainNav": nhắm vào phần tử có id="mainNav" để mở/đóng. -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span> <!-- Icon 3 gạch mặc định của Bootstrap -->
        </button>
        <!-- collapse navbar-collapse: vùng menu sẽ thu gọn/mở rộng trên mobile -->
        <div class="collapse navbar-collapse" id="mainNav">
            <!-- navbar-nav: danh sách các mục điều hướng.
                 me-auto: margin-right tự động, đẩy các phần tử bên phải ra xa.
                 mb-2 mb-lg-0: margin-bottom 0.5rem trên mobile, 0 trên desktop. -->
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- nav-item: mỗi mục trong navbar. isActive('/'): kiểm tra trang chủ -->
                <li class="nav-item"><a class="nav-link <?= isActive('/') ?>" href="/">Trang chủ</a></li>
                <!-- str_starts_with: kiểm tra URL bắt đầu bằng /tra-cuu (xử lý cả /tra-cuu và /tra-cuu/ket-qua) -->
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/tra-cuu') ? 'active' : '' ?>" href="/tra-cuu">Tra cứu</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/tin-tuc') ? 'active' : '' ?>" href="/tin-tuc">Tin tức</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/bien-bao') ? 'active' : '' ?>" href="/bien-bao">Biển báo</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/ban-do') ? 'active' : '' ?>" href="/ban-do">Bản đồ</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/thong-ke') ? 'active' : '' ?>" href="/thong-ke">Thống kê</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/faq') ? 'active' : '' ?>" href="/faq">FAQ</a></li>
            </ul>
            <!-- d-flex: kích hoạt flexbox cho vùng nút bên phải.
                 align-items-center: căn giữa theo chiều dọc.
                 gap-2: khoảng cách giữa các phần tử con là 0.5rem. -->
            <div class="d-flex align-items-center gap-2">
                <?php if ($isLoggedIn): ?>
                    <!-- Trường hợp đã đăng nhập: hiển thị dropdown menu tài khoản -->
                    <div class="dropdown">
                        <!-- btn: lớp cơ bản của nút Bootstrap.
                             btn-outline-light: nút viền trắng, nền trong suốt, chữ trắng.
                             btn-sm: nút kích thước nhỏ.
                             data-bs-toggle="dropdown": kích hoạt dropdown của Bootstrap 5. -->
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <!-- fa-user: icon người dùng. me-1: margin-right 0.25rem -->
                            <i class="fas fa-user me-1"></i>
                            <!-- Cắt ngắn tên người dùng nếu quá 14 ký tự, thêm dấu "...".
                                 mb_strlen: đếm độ dài chuỗi Unicode (hỗ trợ tiếng Việt có dấu).
                                 mb_substr: cắt chuỗi Unicode an toàn.
                                 htmlspecialchars: chống XSS, escape ký tự đặc biệt trong HTML. -->
                            <?= htmlspecialchars(mb_strlen(Session::get('user_name')) > 14 ? mb_substr(Session::get('user_name'), 0, 14) . '...' : Session::get('user_name')) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <!-- fa-tachometer-alt: icon đồng hồ đo (dashboard/tài khoản) -->
                            <li><a class="dropdown-item" href="/tai-khoan"><i class="fas fa-tachometer-alt me-2"></i>Tài khoản</a></li>
                            <!-- fa-car: icon xe hơi (quản lý phương tiện) -->
                            <li><a class="dropdown-item" href="/tai-khoan/phuong-tien"><i class="fas fa-car me-2"></i>Phương tiện</a></li>
                            <!-- fa-history: icon đồng hồ lịch sử -->
                            <li><a class="dropdown-item" href="/tai-khoan/lich-su"><i class="fas fa-history me-2"></i>Lịch sử</a></li>
                            <?php if ($isAdmin): ?>
                                <!-- Đường kẻ phân cách trước mục Admin (chỉ hiện cho admin) -->
                                <li><hr class="dropdown-divider"></li>
                                <!-- fa-cog: icon bánh răng (cài đặt/quản trị) -->
                                <li><a class="dropdown-item" href="/admin"><i class="fas fa-cog me-2"></i>Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <!-- text-danger: chữ đỏ cho hành động đăng xuất -->
                            <li><a class="dropdown-item text-danger" href="/dang-xuat"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Trường hợp chưa đăng nhập: hiển thị nút Đăng nhập và Đăng ký -->
                    <!-- btn-outline-light btn-sm: nút viền trắng nhỏ (Đăng nhập - ít nổi bật hơn) -->
                    <a href="/dang-nhap" class="btn btn-outline-light btn-sm">Đăng nhập</a>
                    <!-- btn-light btn-sm: nút nền trắng chữ xanh (Đăng ký - nổi bật hơn, kêu gọi hành động) -->
                    <a href="/dang-ky" class="btn btn-light btn-sm">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
