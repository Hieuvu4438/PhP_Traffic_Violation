<?php
/**
 * Partial Sidebar - Thanh điều hướng dọc cho trang admin.
 * Chứa danh sách các mục quản lý: Dashboard, Users, Violations, News, Categories,
 * Signs, Locations, FAQs, Alerts, Messages.
 * Được nhúng vào admin.php layout.
 */
/* Lấy URL hiện tại để đánh dấu menu đang active trong sidebar */
$currentUrl = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
/**
 * Hàm helper kiểm tra URL hiện tại có bắt đầu bằng một đường dẫn không.
 * Dùng cho sidebar admin vì mỗi mục có nhiều route con
 * (VD: /admin/users, /admin/users/create, /admin/users/edit/5 đều active mục "Người dùng").
 */
function adminActive(string $path): string {
    global $currentUrl;
    return str_starts_with($currentUrl, $path) ? 'active' : '';
}
?>
<!-- aside: phần tử HTML5 dành cho nội dung phụ (sidebar).
     admin-sidebar: class tùy chỉnh, định nghĩa trong admin.css.
     bg-dark: nền tối (đen/xám đậm).
     text-white: chữ trắng. -->
<aside class="admin-sidebar bg-dark text-white" id="adminSidebar">
    <!-- sidebar-brand: logo/thương hiệu của admin panel.
         p-3: padding 1rem cả 4 phía.
         border-bottom: đường viền dưới.
         border-secondary: viền màu xám. -->
    <div class="sidebar-brand p-3 border-bottom border-secondary">
        <!-- text-decoration-none: bỏ gạch chân link. fw-bold: chữ đậm. -->
        <a href="/admin" class="text-white text-decoration-none fw-bold">
            <!-- fa-shield-alt: icon lá chắn bảo vệ (biểu tượng admin/quản trị) -->
            <i class="fas fa-shield-alt me-2"></i>Admin Panel
        </a>
    </div>
    <!-- sidebar-nav: vùng chứa các liên kết điều hướng. p-2: padding 0.5rem -->
    <nav class="sidebar-nav p-2">
        <!-- nav flex-column: danh sách nav của Bootstrap, xếp dọc (flex-direction: column) -->
        <ul class="nav flex-column">
            <!-- Mục Dashboard: kiểm tra chính xác URL /admin (không dùng adminActive vì sẽ trùng với tất cả).
                 Nếu active thì thêm class bg-primary (nền xanh) để làm nổi bật. -->
            <li class="nav-item">
                <a href="/admin" class="nav-link text-white <?= $currentUrl === '/admin' ? 'active bg-primary' : '' ?>">
                    <!-- fa-tachometer-alt: icon đồng hồ đo (dashboard) -->
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            <!-- fa-users: icon 2 người (quản lý người dùng) -->
            <li class="nav-item">
                <a href="/admin/users" class="nav-link text-white <?= adminActive('/admin/users') ?>">
                    <i class="fas fa-users me-2"></i>Người dùng
                </a>
            </li>
            <!-- fa-exclamation-triangle: icon tam giác cảnh báo (vi phạm) -->
            <li class="nav-item">
                <a href="/admin/violations" class="nav-link text-white <?= adminActive('/admin/violations') ?>">
                    <i class="fas fa-exclamation-triangle me-2"></i>Vi phạm
                </a>
            </li>
            <!-- fa-newspaper: icon tờ báo (tin tức) -->
            <li class="nav-item">
                <a href="/admin/news" class="nav-link text-white <?= adminActive('/admin/news') ?>">
                    <i class="fas fa-newspaper me-2"></i>Tin tức
                </a>
            </li>
            <!-- fa-folder: icon thư mục (danh mục/phân loại) -->
            <li class="nav-item">
                <a href="/admin/categories" class="nav-link text-white <?= adminActive('/admin/categories') ?>">
                    <i class="fas fa-folder me-2"></i>Danh mục
                </a>
            </li>
            <!-- fa-sign: icon biển báo -->
            <li class="nav-item">
                <a href="/admin/signs" class="nav-link text-white <?= adminActive('/admin/signs') ?>">
                    <i class="fas fa-sign me-2"></i>Biển báo
                </a>
            </li>
            <!-- fa-map-marker-alt: icon đánh dấu vị trí bản đồ (địa điểm) -->
            <li class="nav-item">
                <a href="/admin/locations" class="nav-link text-white <?= adminActive('/admin/locations') ?>">
                    <i class="fas fa-map-marker-alt me-2"></i>Địa điểm
                </a>
            </li>
            <!-- fa-question-circle: icon dấu hỏi tròn (FAQ/câu hỏi) -->
            <li class="nav-item">
                <a href="/admin/faqs" class="nav-link text-white <?= adminActive('/admin/faqs') ?>">
                    <i class="fas fa-question-circle me-2"></i>FAQ
                </a>
            </li>
            <!-- fa-bell: icon chuông (cảnh báo/thông báo) -->
            <li class="nav-item">
                <a href="/admin/alerts" class="nav-link text-white <?= adminActive('/admin/alerts') ?>">
                    <i class="fas fa-bell me-2"></i>Cảnh báo
                </a>
            </li>
            <!-- fa-envelope: icon phong bì thư (tin nhắn liên hệ) -->
            <li class="nav-item">
                <a href="/admin/messages" class="nav-link text-white <?= adminActive('/admin/messages') ?>">
                    <i class="fas fa-envelope me-2"></i>Tin nhắn
                </a>
            </li>
            <li class="nav-item">
                <a href="/admin/chat" class="nav-link text-white <?= adminActive('/admin/chat') ?>">
                    <i class="fas fa-comments me-2"></i>Chat khách hàng
                </a>
            </li>
            <!-- Phân cách giữa menu quản lý và menu tiện ích.
                 mt-3: margin-top 1rem.
                 pt-2: padding-top 0.5rem.
                 border-top: đường viền trên.
                 border-secondary: viền màu xám. -->
            <li class="nav-item mt-3 pt-2 border-top border-secondary">
                <!-- target="_blank": mở link trong tab mới.
                     text-white-50: chữ trắng với độ mờ 50% (ít nổi bật hơn menu chính). -->
                <a href="/" class="nav-link text-white-50" target="_blank">
                    <!-- fa-external-link-alt: icon mở liên kết ngoài -->
                    <i class="fas fa-external-link-alt me-2"></i>Xem website
                </a>
            </li>
            <li class="nav-item">
                <a href="/dang-xuat" class="nav-link text-white-50">
                    <!-- fa-sign-out-alt: icon đăng xuất (mũi tên ra khỏi cửa) -->
                    <i class="fas fa-sign-out-alt me-2"></i>Đăng xuất
                </a>
            </li>
        </ul>
    </nav>
</aside>
