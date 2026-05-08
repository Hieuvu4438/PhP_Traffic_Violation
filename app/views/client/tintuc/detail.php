<?php
use App\Core\Helper;
?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/tin-tuc" class="text-decoration-none">Tin tức</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
    </nav>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row">
        <!-- Article Content -->
        <div class="col-lg-8">
            <article class="card shadow-sm border-0">
                <?php if (!empty($article['thumbnail'])): ?>
                    <img src="/<?= htmlspecialchars($article['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                         class="card-img-top" alt="<?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>"
                         style="max-height: 400px; object-fit: cover;">
                <?php endif; ?>

                <div class="card-body p-4">
                    <h1 class="card-title fw-bold mb-3">
                        <?= htmlspecialchars($article['title'], ENT_QUOTES, 'UTF-8') ?>
                    </h1>

                    <div class="d-flex flex-wrap gap-3 mb-4 text-muted small">
                        <?php if (!empty($article['category_name'])): ?>
                            <span>
                                <i class="fas fa-folder me-1"></i>
                                <?= htmlspecialchars($article['category_name'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                        <span>
                            <i class="far fa-calendar me-1"></i>
                            <?= htmlspecialchars(Helper::formatDateTime($article['created_at']), ENT_QUOTES, 'UTF-8') ?>
                        </span>
                        <?php if (!empty($article['author_name'])): ?>
                            <span>
                                <i class="far fa-user me-1"></i>
                                <?= htmlspecialchars($article['author_name'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        <?php endif; ?>
                        <span>
                            <i class="far fa-eye me-1"></i>
                            <?= (int)($article['views'] ?? 0) ?> lượt xem
                        </span>
                    </div>

                    <hr>

                    <div class="article-content">
                        <?= $article['content'] ?>
                    </div>
                </div>
            </article>

            <!-- Back -->
            <div class="mt-3">
                <a href="/tin-tuc" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách tin tức
                </a>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <!-- Article Info -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-info-circle me-2"></i>Thông tin bài viết
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-folder text-primary me-2"></i>
                            <strong>Danh mục:</strong>
                            <?= htmlspecialchars($article['category_name'] ?? 'Chưa phân loại', ENT_QUOTES, 'UTF-8') ?>
                        </li>
                        <li class="mb-2">
                            <i class="far fa-calendar text-primary me-2"></i>
                            <strong>Ngày đăng:</strong>
                            <?= htmlspecialchars(Helper::formatDate($article['created_at'], 'd/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                        </li>
                        <li>
                            <i class="far fa-eye text-primary me-2"></i>
                            <strong>Lượt xem:</strong>
                            <?= (int)($article['views'] ?? 0) ?>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Related Articles -->
            <?php if (!empty($related)): ?>
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="fas fa-link me-2"></i>Bài viết liên quan
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($related as $rel): ?>
                                <a href="/tin-tuc/<?= htmlspecialchars($rel['slug'] ?? 'bai-viet-' . $rel['id'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="list-group-item list-group-item-action">
                                    <div class="d-flex gap-2">
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
