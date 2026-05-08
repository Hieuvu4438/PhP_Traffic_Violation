<?php use App\Core\Helper; ?>

<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/bien-bao" class="text-decoration-none">Biển báo</a></li>
            <li class="breadcrumb-item active"><?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
    </nav>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row">
        <!-- Sign Detail -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="row g-0">
                    <?php if (!empty($sign['image'])): ?>
                        <div class="col-md-4 bg-light d-flex align-items-center justify-content-center p-3">
                            <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>"
                                 class="img-fluid" alt="<?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?>"
                                 style="max-height: 250px; object-fit: contain;">
                        </div>
                    <?php endif; ?>
                    <div class="col-md-<?= !empty($sign['image']) ? '8' : '12' ?>">
                        <div class="card-body p-4">
                            <span class="badge bg-secondary mb-2">
                                <?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <h2 class="card-title fw-bold mb-3">
                                <?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?>
                            </h2>

                            <?php if (!empty($group)): ?>
                                <p class="mb-2">
                                    <strong>Nhóm:</strong>
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <?php if (!empty($sign['description'])): ?>
                                <hr>
                                <h5 class="fw-bold">Mô tả</h5>
                                <p class="text-muted">
                                    <?= nl2br(htmlspecialchars($sign['description'], ENT_QUOTES, 'UTF-8')) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-3">
                <a href="/bien-bao" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách biển báo
                </a>
            </div>
        </div>

        <!-- Sidebar: Related Signs -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <?php if (!empty($group)): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-info text-white fw-bold">
                        <i class="fas fa-folder me-2"></i>Cùng nhóm: <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            <?php foreach ($related as $rel): ?>
                                <a href="/bien-bao/<?= htmlspecialchars($rel['id'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="list-group-item list-group-item-action <?= (int) $rel['id'] === (int) $sign['id'] ? 'active' : '' ?>">
                                    <span class="badge bg-secondary me-2"><?= htmlspecialchars($rel['sign_code'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?= htmlspecialchars($rel['name'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Quick Links -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-link me-2"></i>Liên kết nhanh
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="/tra-cuu" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-2"></i>Tra cứu phạt nguội
                        </a>
                        <?php foreach (array_slice($related, 0, 3) as $rel): ?>
                            <?php if ((int) $rel['id'] !== (int) $sign['id']): ?>
                                <a href="/bien-bao/<?= htmlspecialchars($rel['id'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="btn btn-outline-secondary btn-sm text-start">
                                    <?= htmlspecialchars($rel['sign_code'], ENT_QUOTES, 'UTF-8') ?> -
                                    <?= htmlspecialchars(mb_substr($rel['name'], 0, 30), ENT_QUOTES, 'UTF-8') ?><?= mb_strlen($rel['name']) > 30 ? '...' : '' ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
