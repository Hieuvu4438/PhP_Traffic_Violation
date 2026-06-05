<?php
use App\Core\Helper;
?>

<!--
  Trang chi tiết tin tức (client/tintuc/detail.php)
  - Breadcrumb điều hướng (Trang chủ > Tin tức > Tên bài)
  - Cột trái: nội dung bài viết (thumbnail, metadata, nội dung HTML/CKEditor)
  - Cột phải: sidebar thông tin bài viết + bài viết liên quan
  - $article: dữ liệu bài viết chính
  - $related: danh sách bài viết liên quan (cùng danh mục)
-->

<div class="container py-4">
    <!-- === Breadcrumb: điều hướng phân cấp === -->
    <!-- aria-label="breadcrumb": cho accessibility (screen reader) -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="/tin-tuc" class="text-decoration-none">News</a></li>
            <!-- breadcrumb-item active: mục hiện tại (không click được) -->
            <li class="breadcrumb-item active"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
    </nav>

    <!-- Partial hiển thị thông báo từ session -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row">
        <!-- === Cột trái: Nội dung bài viết (8/12 trên desktop) === -->
        <div class="col-lg-8">
            <!-- article: thẻ semantic cho nội dung bài viết -->
            <article class="card shadow-sm border-0">
                <!-- Thumbnail bài viết (nếu có) -->
                <?php if (!empty($article['thumbnail'])): ?>
                    <!-- max-height: 400px - giới hạn chiều cao ảnh | object-fit: cover -->
                    <img src="/<?= htmlspecialchars($article['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                         class="card-img-top" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>"
                         style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>

                <!-- p-4: padding 1.5rem cho nội dung bài viết -->
                <div class="card-body p-4">
                    <!-- Tiêu đề bài viết | fw-bold: in đậm -->
                    <h1 class="card-title fw-bold mb-3">
                        <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <!-- Metadata: danh mục, ngày đăng, tác giả, lượt xem -->
                    <!-- d-flex flex-wrap gap-3: flexbox với các item gói xuống dòng, gap 1rem -->
                    <div class="d-flex flex-wrap gap-3 mb-4 text-muted small">
                        <!-- Danh mục -->
                        <?php if (!empty($article['category_name'])): ?>
                            <span>
                                <!-- fa-folder: icon thư mục (danh mục) -->
                                <i class="fas fa-folder me-1"></i>
                                <?= htmlspecialchars($article['category_name'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                        <!-- Ngày đăng -->
                        <span>
                            <!-- far fa-calendar: icon lịch -->
                            <i class="far fa-calendar me-1"></i>
                            <!-- Helper::formatDateTime: định dạng ngày giờ đầy đủ -->
                            <?= htmlspecialchars(Helper::formatDateTime($article['created_at']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <!-- Tác giả (nếu có) -->
                        <?php if (!empty($article['author_name'])): ?>
                            <span>
                                <!-- far fa-user: icon người dùng -->
                                <i class="far fa-user me-1"></i>
                                <?= htmlspecialchars($article['author_name'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                        <!-- Lượt xem -->
                        <span>
                            <!-- far fa-eye: icon con mắt (lượt xem) -->
                            <i class="far fa-eye me-1"></i>
                            <?= (int)($article['views'] ?? 0) ?> views
                        </span>
                    </div>

                    <!-- Đường kẻ ngang phân cách metadata và nội dung -->
                    <hr>

                    <!-- Nội dung bài viết: output HTML thô từ CKEditor (đã lưu trong database) -->
                    <!-- KHÔNG dùng htmlspecialchars ở đây vì content là HTML có chủ đích -->
                    <div class="article-content">
                        <?= $article['content'] ?>
                    </div>
                </div>
            </article>

            <!-- Nút quay lại danh sách tin tức -->
            <div class="mt-3">
                <!-- btn-outline-secondary: nút viền xám -->
                <a href="/tin-tuc" class="btn btn-outline-secondary">
                    <!-- fa-arrow-left: icon mũi tên sang trái (quay lại) -->
                    <i class="fas fa-arrow-left me-2"></i>Back to News List
                </a>
            </div>
        </div>

        <!-- === Cột phải: Sidebar (4/12 trên desktop) === -->
        <!-- mt-4 mt-lg-0: margin-top 1.5rem trên mobile, 0 trên desktop -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <!-- === Thông tin bài viết === -->
            <div class="card shadow-sm border-0 mb-4">
                <!-- card-header bg-primary text-white: header xanh chữ trắng -->
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-info-circle: icon thông tin -->
                    <i class="fas fa-info-circle me-2"></i>Article Information
                </div>
                <div class="card-body">
                    <!-- list-unstyled: bỏ bullet mặc định của ul -->
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <!-- fa-folder text-primary: icon thư mục màu xanh -->
                            <i class="fas fa-folder text-primary me-2"></i>
                            <strong>Category:</strong>
                            <?= htmlspecialchars($article['category_name'] ?? 'Uncategorized', ENT_QUOTES, 'UTF-8') ?>
                        </li>
                        <li class="mb-2">
                            <i class="far fa-calendar text-primary me-2"></i>
                            <strong>Published:</strong>
                            <?= htmlspecialchars(Helper::formatDate($article['created_at'], 'd/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                        </li>
                        <li>
                            <i class="far fa-eye text-primary me-2"></i>
                            <strong>Views:</strong>
                            <?= (int)($article['views'] ?? 0) ?>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- === Bài viết liên quan (nếu có) === -->
            <?php if (!empty($related)): ?>
                <div class="card shadow-sm border-0">
                    <!-- card-header bg-info text-white: header xanh nhạt chữ trắng -->
                    <div class="card-header bg-info text-white fw-bold">
                        <!-- fa-link: icon mắt xích (liên kết) -->
                        <i class="fas fa-link me-2"></i>Related Articles
                    </div>
                    <!-- p-0: không padding để list-group sát viền -->
                    <div class="card-body p-0">
                        <!-- list-group-flush: bỏ bo góc ngoài -->
                        <div class="list-group list-group-flush">
                            <?php foreach ($related as $rel): ?>
                                <a href="/tin-tuc/<?= htmlspecialchars($rel['slug'] ?? 'bai-viet-' . $rel['id'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="list-group-item list-group-item-action">
                                    <!-- d-flex gap-2: flexbox với gap 0.5rem giữa ảnh và chữ -->
                                    <div class="d-flex gap-2">
                                        <!-- Thumbnail nhỏ 60x60 của bài liên quan -->
                                        <?php if (!empty($rel['thumbnail'])): ?>
                                            <img src="/<?= htmlspecialchars($rel['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                                                 alt="" style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1"><?= htmlspecialchars($rel['title'], ENT_QUOTES, 'UTF-8') ?></h6>
                                            <small class="text-muted">
                                                <i class="far fa-calendar me-1"></i>
                                                <?= htmlspecialchars(Helper::formatDate($rel['created_at'], 'd/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
