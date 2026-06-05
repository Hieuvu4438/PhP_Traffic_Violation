<?php
/**
 * Layout cho các trang phía người dùng (client-side).
 * Đây là "khung xương" bao bọc toàn bộ giao diện người dùng:
 * header (navbar) + nội dung chính + footer.
 * Mỗi controller gọi $this->view('...', $data) sẽ tự động nhúng layout này.
 */
/* Import Session helper để kiểm tra đăng nhập, CSRF token, flash messages */
use App\Core\Session;
?>
<!DOCTYPE html>
<!-- lang="vi": khai báo ngôn ngữ tiếng Việt cho SEO và trình đọc màn hình -->
<html lang="vi">
<head>
    <!-- UTF-8: hỗ trợ đầy đủ tiếng Việt có dấu -->
    <meta charset="UTF-8">
    <!-- viewport: đảm bảo responsive trên mobile, width=device-width = rộng bằng màn hình thiết bị -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Meta description cho SEO: mô tả trang web trên kết quả tìm kiếm -->
    <meta name="description" content="Tra cứu phạt nguội toàn quốc - Kiểm tra vi phạm giao thông qua biển số xe. Dữ liệu từ Cục CSGT & Cục Đăng Kiểm Việt Nam.">
    <!-- htmlspecialchars: chống XSS - escape dữ liệu động trong <title>.
         $title ?? '...': toán tử null coalescing, nếu $title không được truyền thì dùng chuỗi mặc định -->
    <title><?= htmlspecialchars($title ?? 'Tra Cứu Phương Tiện Vi Phạm Giao Thông') ?></title>
    <!-- Bootstrap 5.3.3 CSS từ CDN: framework CSS phổ biến, cung cấp grid, component, utility class -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5.1: thư viện icon vector (fa-solid, fa-brands...), tải từ CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts Inter: font hỗ trợ đầy đủ tiếng Việt có dấu -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- style.css: CSS tùy chỉnh của dự án, ghi đè và mở rộng Bootstrap -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<!-- Nhúng partial header.php: chứa navbar, dropdown người dùng, menu điều hướng chính -->
<?php require __DIR__ . '/../partials/header.php'; ?>

<!-- main: vùng nội dung chính của từng trang.
     $content được controller render ra, nhúng vào đây -->
<main class="main-content">
    <?= $content ?>
</main>

<!-- Nhúng partial footer.php: thông tin liên hệ, liên kết nhanh, copyright -->
<?php require __DIR__ . '/../partials/footer.php'; ?>

<!-- Bootstrap 5 JS bundle: bao gồm Popper.js (cho dropdown, tooltip) + Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- main.js: JavaScript tùy chỉnh của dự án (phía client) -->
<script src="/assets/js/main.js"></script>
</body>
</html>
