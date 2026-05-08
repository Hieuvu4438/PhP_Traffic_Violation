<?php
use App\Core\Session;
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Tra cứu phạt nguội toàn quốc - Kiểm tra vi phạm giao thông qua biển số xe. Dữ liệu từ Cục CSGT & Cục Đăng Kiểm Việt Nam.">
    <title><?= htmlspecialchars($title ?? 'Tra Cứu Phương Tiện Vi Phạm Giao Thông') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<?php require __DIR__ . '/../partials/header.php'; ?>

<main class="main-content">
    <?= $content ?>
</main>

<?php require __DIR__ . '/../partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
