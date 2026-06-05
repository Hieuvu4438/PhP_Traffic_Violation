<?php use App\Core\Helper; ?>

<!--
  Trang chi tiết biển báo giao thông (client/bienbao/detail.php)
  - Breadcrumb: Trang chủ > Biển báo > Mã biển
  - Cột trái: thẻ biển báo (ảnh + mã + tên + nhóm + mô tả)
  - Cột phải: sidebar - biển báo cùng nhóm + liên kết nhanh
  - $sign: dữ liệu biển báo hiện tại
  - $group: thông tin nhóm của biển báo này
  - $related: danh sách biển báo cùng nhóm
-->

<div class="container py-4">
    <!-- === Breadcrumb điều hướng === -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/" class="text-decoration-none">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="/bien-bao" class="text-decoration-none">Biển báo</a></li>
            <!-- Hiển thị mã biển báo (VD: P.102) thay vì tên đầy đủ -->
            <li class="breadcrumb-item active"><?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?></li>
        </ol>
    </nav>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row">
        <!-- === Cột trái: Chi tiết biển báo (8/12 desktop) === -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <!-- row g-0: xóa gutter giữa các cột trong card -->
                <div class="row g-0">
                    <!-- Ảnh biển báo: col-md-4, căn giữa, nền xám nhạt -->
                    <?php if (!empty($sign['image'])): ?>
                        <div class="col-md-4 bg-light d-flex align-items-center justify-content-center p-3">
                            <!-- img-fluid: ảnh responsive, tự co giãn theo container -->
                            <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>"
                                 class="img-fluid" alt="<?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?>"
                                 style="max-height: 250px; object-fit: contain;">
                        </div>
                    <?php endif; ?>
                    <!-- Nội dung chi tiết: col-md-8 (nếu có ảnh) hoặc col-md-12 (không ảnh) -->
                    <div class="col-md-<?= !empty($sign['image']) ? '8' : '12' ?>">
                        <div class="card-body p-4">
                            <!-- Mã biển báo dạng badge -->
                            <span class="badge bg-secondary mb-2">
                                <?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?>
                            </span>
                            <!-- Tên biển báo -->
                            <h2 class="card-title fw-bold mb-3">
                                <?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?>
                            </h2>

                            <!-- Tên nhóm biển báo (nếu có $group) -->
                            <?php if (!empty($group)): ?>
                                <p class="mb-2">
                                    <strong>Nhóm:</strong>
                                    <!-- badge bg-primary: huy hiệu xanh hiển thị tên nhóm -->
                                    <span class="badge bg-primary">
                                        <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </span>
                                </p>
                            <?php endif; ?>

                            <!-- Mô tả biển báo (nếu có) -->
                            <?php if (!empty($sign['description'])): ?>
                                <hr>
                                <h5 class="fw-bold">Mô tả</h5>
                                <!-- nl2br: chuyển newline thành <br> | text-muted: chữ xám -->
                                <p class="text-muted">
                                    <?= nl2br(htmlspecialchars($sign['description'], ENT_QUOTES, 'UTF-8')) ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Nút quay lại -->
            <div class="mt-3">
                <a href="/bien-bao" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách biển báo
                </a>
            </div>
        </div>

        <!-- === Cột phải: Sidebar (4/12 desktop) === -->
        <!-- mt-4 mt-lg-0: margin-top trên mobile, 0 trên desktop -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <!-- === Biển báo cùng nhóm === -->
            <?php if (!empty($group)): ?>
                <div class="card shadow-sm border-0 mb-4">
                    <!-- card-header bg-info text-white: header xanh nhạt chữ trắng -->
                    <div class="card-header bg-info text-white fw-bold">
                        <!-- fa-folder: icon thư mục (phân nhóm) -->
                        <i class="fas fa-folder me-2"></i>Cùng nhóm: <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                    <div class="card-body p-0">
                        <!-- list-group-flush: danh sách sát viền -->
                        <div class="list-group list-group-flush">
                            <!-- Duyệt qua tất cả biển báo cùng nhóm, active class cho biển hiện tại -->
                            <?php foreach ($related as $rel): ?>
                                <a href="/bien-bao/<?= htmlspecialchars($rel['id'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="list-group-item list-group-item-action <?= (int) $rel['id'] === (int) $sign['id'] ? 'active' : '' ?>">
                                    <!-- badge bg-secondary: huy hiệu xám hiển thị mã biển -->
                                    <span class="badge bg-secondary me-2"><?= htmlspecialchars($rel['sign_code'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <?= htmlspecialchars($rel['name'], ENT_QUOTES, 'UTF-8') ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- === Liên kết nhanh === -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-link: icon mắt xích liên kết -->
                    <i class="fas fa-link me-2"></i>Liên kết nhanh
                </div>
                <div class="card-body">
                    <!-- d-grid gap-2: các nút xếp dọc grid, gap 0.5rem -->
                    <div class="d-grid gap-2">
                        <!-- Link tra cứu phạt nguội -->
                        <a href="/tra-cuu" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-2"></i>Tra cứu phạt nguội
                        </a>
                        <!-- Hiển thị tối đa 3 biển báo liên quan khác (không trùng với biển hiện tại) -->
                        <?php foreach (array_slice($related, 0, 3) as $rel): ?>
                            <?php if ((int) $rel['id'] !== (int) $sign['id']): ?>
                                <a href="/bien-bao/<?= htmlspecialchars($rel['id'], ENT_QUOTES, 'UTF-8') ?>"
                                   class="btn btn-outline-secondary btn-sm text-start">
                                    <!-- Mã biển - Tên biển (cắt 30 ký tự) -->
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
