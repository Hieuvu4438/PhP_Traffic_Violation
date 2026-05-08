<?php
$currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
function adminActive(string $path): string {
    global $currentUrl;
    return str_starts_with($currentUrl, $path) ? 'active' : '';
}
?>
<aside class="admin-sidebar bg-dark text-white" id="adminSidebar">
    <div class="sidebar-brand p-3 border-bottom border-secondary">
        <a href="/admin" class="text-white text-decoration-none fw-bold">
            <i class="fas fa-shield-alt me-2"></i>Admin Panel
        </a>
    </div>
    <nav class="sidebar-nav p-2">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a href="/admin" class="nav-link text-white <?= $currentUrl === '/admin' ? 'active bg-primary' : '' ?>">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/users" class="nav-link text-white <?= adminActive('/admin/users') ?>">
                    <i class="fas fa-users me-2"></i>Người dùng
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/violations" class="nav-link text-white <?= adminActive('/admin/violations') ?>">
                    <i class="fas fa-exclamation-triangle me-2"></i>Vi phạm
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/news" class="nav-link text-white <?= adminActive('/admin/news') ?>">
                    <i class="fas fa-newspaper me-2"></i>Tin tức
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/categories" class="nav-link text-white <?= adminActive('/admin/categories') ?>">
                    <i class="fas fa-folder me-2"></i>Danh mục
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/signs" class="nav-link text-white <?= adminActive('/admin/signs') ?>">
                    <i class="fas fa-sign me-2"></i>Biển báo
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/locations" class="nav-link text-white <?= adminActive('/admin/locations') ?>">
                    <i class="fas fa-map-marker-alt me-2"></i>Địa điểm
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/faqs" class="nav-link text-white <?= adminActive('/admin/faqs') ?>">
                    <i class="fas fa-question-circle me-2"></i>FAQ
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/alerts" class="nav-link text-white <?= adminActive('/admin/alerts') ?>">
                    <i class="fas fa-bell me-2"></i>Cảnh báo
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/messages" class="nav-link text-white <?= adminActive('/admin/messages') ?>">
                    <i class="fas fa-envelope me-2"></i>Tin nhắn
                </a>
            </li>
            <li class="nav-item mt-3 pt-2 border-top border-secondary">
                <a href="/" class="nav-link text-white-50" target="_blank">
                    <i class="fas fa-external-link-alt me-2"></i>Xem website
                </a>
            </li>
            <li class="nav-item">
                <a href="/dang-xuat" class="nav-link text-white-50">
                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                </a>
            </li>
        </ul>
    </nav>
</aside>
