<?php use App\Core\Session; ?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-sign me-2 text-primary"></i>Tra cứu biển báo giao thông
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <form method="GET" action="/bien-bao" class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" name="q"
                               placeholder="Tìm kiếm biển báo (tên, mã...)"
                               value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <button type="submit" class="btn btn-primary">Tìm</button>
                        <?php if (!empty($keyword)): ?>
                            <a href="/bien-bao" class="btn btn-outline-secondary">Xóa</a>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="/bien-bao" class="btn <?= empty($currentGroup) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                            Tất cả
                        </a>
                        <?php foreach ($groups as $group): ?>
                            <a href="/bien-bao?group=<?= htmlspecialchars($group['id'], ENT_QUOTES, 'UTF-8') ?>"
                               class="btn <?= ($currentGroup ?? '') === (string) $group['id'] ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Signs List -->
    <?php if (empty($signsByGroup)): ?>
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Không tìm thấy biển báo nào</h4>
            <p class="text-muted">Thử tìm kiếm với từ khóa khác.</p>
            <a href="/bien-bao" class="btn btn-outline-primary">Xem tất cả</a>
        </div>
    <?php else: ?>
        <?php foreach ($signsByGroup as $groupName => $signs): ?>
            <?php if (!empty($groupName)): ?>
                <div class="mb-4">
                    <h4 class="fw-bold border-bottom pb-2 mb-3 text-primary">
                        <i class="fas fa-folder me-2"></i><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>
                        <span class="badge bg-secondary ms-2"><?= count($signs) ?></span>
                    </h4>
            <?php endif; ?>

            <div class="row g-3 mb-4">
                <?php foreach ($signs as $sign): ?>
                    <div class="col-md-4 col-lg-3 col-6">
                        <a href="/bien-bao/<?= htmlspecialchars($sign['id'], ENT_QUOTES, 'UTF-8') ?>"
                           class="text-decoration-none">
                            <div class="card shadow-sm h-100 border-0 sign-card">
                                <?php if (!empty($sign['image'])): ?>
                                    <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>"
                                         class="card-img-top p-3" alt="<?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?>"
                                         style="height: 140px; object-fit: contain;">
                                <?php else: ?>
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-3"
                                         style="height: 140px;">
                                        <i class="fas fa-sign fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="card-body text-center p-2">
                                    <span class="badge bg-secondary mb-1"><?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <p class="card-text small mb-0 text-dark">
                                        <?= htmlspecialchars(mb_strlen($sign['name']) > 60 ? mb_substr($sign['name'], 0, 60) . '...' : $sign['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (!empty($groupName)): ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.sign-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.sign-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>
