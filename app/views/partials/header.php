<?php
use App\Core\Session;
$isLoggedIn = Session::isLoggedIn();
$isAdmin = Session::isAdmin();
$currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function isActive(string $path): string {
    global $currentUrl;
    return $currentUrl === $path ? 'active' : '';
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            <i class="fas fa-car me-2"></i>Tra Cứu Phạt Nguội
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link <?= isActive('/') ?>" href="/">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/tra-cuu') ? 'active' : '' ?>" href="/tra-cuu">Tra cứu</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/tin-tuc') ? 'active' : '' ?>" href="/tin-tuc">Tin tức</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/bien-bao') ? 'active' : '' ?>" href="/bien-bao">Biển báo</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/ban-do') ? 'active' : '' ?>" href="/ban-do">Bản đồ</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/thong-ke') ? 'active' : '' ?>" href="/thong-ke">Thống kê</a></li>
                <li class="nav-item"><a class="nav-link <?= str_starts_with($currentUrl, '/faq') ? 'active' : '' ?>" href="/faq">FAQ</a></li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <?php if ($isLoggedIn): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i><?= htmlspecialchars(mb_strlen(Session::get('user_name')) > 14 ? mb_substr(Session::get('user_name'), 0, 14) . '...' : Session::get('user_name')) ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/tai-khoan"><i class="fas fa-tachometer-alt me-2"></i>Tài khoản</a></li>
                            <li><a class="dropdown-item" href="/tai-khoan/phuong-tien"><i class="fas fa-car me-2"></i>Phương tiện</a></li>
                            <li><a class="dropdown-item" href="/tai-khoan/lich-su"><i class="fas fa-history me-2"></i>Lịch sử</a></li>
                            <?php if ($isAdmin): ?>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/admin"><i class="fas fa-cog me-2"></i>Admin</a></li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="/dang-xuat"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <a href="/dang-nhap" class="btn btn-outline-light btn-sm">Đăng nhập</a>
                    <a href="/dang-ky" class="btn btn-light btn-sm">Đăng ký</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
