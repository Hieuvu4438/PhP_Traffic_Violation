<?php
use App\Core\Session;
if (!Session::isLoggedIn() || !Session::isAdmin()) {
    header('Location: /dang-nhap');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?= htmlspecialchars($title ?? 'Dashboard') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>

<div class="admin-wrapper">
    <?php require __DIR__ . '/../partials/sidebar.php'; ?>

    <div class="admin-main">
        <header class="admin-header d-flex justify-content-between align-items-center">
            <button id="sidebarToggle" class="btn btn-outline-secondary btn-sm d-md-none">
                <i class="fas fa-bars"></i>
            </button>
            <span class="text-muted d-none d-md-block">Admin Dashboard</span>
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-user-circle me-1"></i>
                    <?= htmlspecialchars(Session::get('user_name')) ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="/tai-khoan/ho-so"><i class="fas fa-user-edit me-2"></i>Hồ sơ</a></li>
                    <li><a class="dropdown-item" href="/"><i class="fas fa-external-link-alt me-2"></i>Xem website</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="/dang-xuat"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                </ul>
            </div>
        </header>

        <div class="admin-content">
            <?= $content ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/admin.js"></script>
</body>
</html>
