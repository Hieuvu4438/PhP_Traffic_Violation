<?php
use App\Core\Helper;
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-newspaper me-2 text-primary"></i>Tin tức giao thông
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row">
        <!-- Category Filter Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-list me-2"></i>Danh mục
                </div>
                <div class="list-group list-group-flush">
                    <a href="/tin-tuc"
                       class="list-group-item list-group-item-action <?= ($currentCategory ?? '') === '' ? 'active' : '' ?>">
                        Tất cả
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="/tin-tuc?cat=<?= htmlspecialchars($cat['id'], ENT_QUOTES, 'UTF-8') ?>"
                           class="list-group-item list-group-item-action <?= ($currentCategory ?? '') === (string) $cat['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- News Grid -->
        <div class="col-lg-9">
            <?php if (empty($news)): ?>
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">Chưa có bài viết nào</h4>
                    <p class="text-muted">Vui lòng quay lại sau.</p>
                </div>
            <?php else: ?>
                <div class="row g-4">
                    <?php foreach ($news as $item): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="card shadow-sm h-100 border-0">
                                <?php if (!empty($item['thumbnail'])): ?>
                                    <img src="/<?= htmlspecialchars($item['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                                         class="card-img-top" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"
                                         style="height: 180px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                         style="height: 180px;">
                                        <i class="fas fa-newspaper fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body d-flex flex-column">
                                    <?php if (!empty($item['category_name'])): ?>
                                        <span class="badge bg-primary mb-2 align-self-start">
                                            <?= htmlspecialchars($item['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                    <h5 class="card-title">
                                        <a href="/tin-tuc/<?= htmlspecialchars($item['slug'] ?? 'bai-viet-' . $item['id'], ENT_QUOTES, 'UTF-8') ?>"
                                           class="text-decoration-none text-dark stretched-link">
                                            <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?= htmlspecialchars(Helper::truncate(strip_tags($item['content'] ?? ''), 100), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <i class="far fa-calendar me-1"></i>
                                            <?= htmlspecialchars(Helper::formatDate($item['created_at'], 'd/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                        <small class="text-muted">
                                            <i class="far fa-eye me-1"></i><?= (int)($item['views'] ?? 0) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if (isset($pagination)): ?>
                    <?php
                    $baseUrl = '/tin-tuc';
                    if (!empty($currentCategory)) {
                        $baseUrl .= '?cat=' . $currentCategory;
                    }
                    require __DIR__ . '/../../partials/pagination.php';
                    ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
