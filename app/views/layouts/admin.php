<?php
/**
 * Layout cho các trang quản trị (admin panel).
 * Bao gồm: sidebar điều hướng + header admin + vùng nội dung chính.
 * Controller gọi $this->view('admin/folder/file', $data, 'admin') để dùng layout này.
 */
use App\Core\Session;
/* Bảo vệ trang admin: nếu chưa đăng nhập HOẶC không phải admin thì chuyển hướng về /dang-nhap */
if (!Session::isLoggedIn() || !Session::isAdmin()) {
    header('Location: /dang-nhap');
    exit; // Dừng thực thi ngay sau khi redirect
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- CSRF token: nhúng token vào meta tag để JavaScript có thể đọc và gửi kèm AJAX request -->
    <meta name="csrf-token" content="<?= htmlspecialchars(Session::csrfToken()) ?>">
    <title>Admin - <?= htmlspecialchars($title ?? 'Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts Inter: font hỗ trợ đầy đủ tiếng Việt có dấu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- admin.css: CSS riêng cho giao diện admin (sidebar, header, bảng dữ liệu...) -->
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<!-- admin-wrapper: bọc toàn bộ layout admin, dùng flexbox để sidebar và main nằm ngang -->
<div class="admin-wrapper">
    <!-- Nhúng sidebar.php: thanh điều hướng dọc bên trái cho admin -->
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <!-- admin-main: vùng bên phải sidebar, chứa header admin + nội dung trang -->
    <div class="admin-main">
        <!-- admin-header: thanh tiêu đề trên cùng của admin.
             d-flex: kích hoạt flexbox.
             justify-content-between: đẩy 2 phần tử ra 2 đầu (nút toggle trái, dropdown phải).
             align-items-center: căn giữa các phần tử theo chiều dọc. -->
        <header class="admin-header d-flex justify-content-between align-items-center">
            <!-- Nút toggle sidebar cho mobile.
                 btn btn-outline-secondary btn-sm: nút viền xám, kích thước nhỏ.
                 d-md-none: chỉ hiển thị trên màn hình <768px (mobile), ẩn trên desktop. -->
            <button id="sidebarToggle" class="btn btn-outline-secondary btn-sm d-md-none">
                <i class="fas fa-bars"></i> <!-- Icon 3 gạch ngang (hamburger menu) -->
            </button>
            <!-- d-none d-md-block: ẩn trên mobile, hiển thị trên màn hình ≥768px.
                 text-muted: chữ màu xám nhạt (secondary text) -->
            <span class="text-muted d-none d-md-block">Admin Dashboard</span>
            <!-- Dropdown menu cho tài khoản admin -->
            <div class="dropdown">
                <!-- data-bs-toggle="dropdown": thuộc tính Bootstrap 5 kích hoạt dropdown khi click -->
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <!-- fa-user-circle: icon hình người dùng có viền tròn. me-1: margin-right 0.25rem -->
                    <i class="fas fa-user-circle me-1"></i>
                    <?= htmlspecialchars(Session::get('user_name')) ?>
                </button>
                <!-- dropdown-menu-end: căn dropdown về bên phải, tránh tràn màn hình -->
                <ul class="dropdown-menu dropdown-menu-end">
                    <!-- fa-user-edit: icon chỉnh sửa hồ sơ -->
                    <li><a class="dropdown-item" href="/tai-khoan/ho-so"><i class="fas fa-user-edit me-2"></i>Hồ sơ</a></li>
                    <!-- fa-external-link-alt: icon mở liên kết ngoài (mũi tên chéo) -->
                    <li><a class="dropdown-item" href="/"><i class="fas fa-external-link-alt me-2"></i>Xem website</a></li>
                    <!-- dropdown-divider: đường kẻ ngang phân cách các mục trong dropdown -->
                    <li><hr class="dropdown-divider"></li>
                    <!-- text-danger: chữ màu đỏ, báo hiệu hành động quan trọng (đăng xuất).
                         fa-sign-out-alt: icon mũi tên ra khỏi cửa (đăng xuất) -->
                    <li><a class="dropdown-item text-danger" href="/dang-xuat"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                </ul>
            </div>
        </header>

        <!-- admin-content: vùng nội dung chính của mỗi trang admin -->
        <div class="admin-content">
            <?= $content ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- admin.js: JavaScript riêng cho admin (toggle sidebar, xác nhận xóa, biểu đồ...) -->
<script src="/assets/js/admin.js"></script>
</body>
</html>
