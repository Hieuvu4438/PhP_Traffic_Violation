<?php
require __DIR__ . '/../../partials/alerts.php';
use App\Core\Session;
$isEdit = isset($alert);
$old = $isEdit ? $alert : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '/admin/alerts/' . $alert['id'] : '/admin/alerts' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['title'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Loại cảnh báo</label>
                    <select name="alert_type" class="form-select">
                        <option value="accident" <?= ($old['alert_type'] ?? 'other') === 'accident' ? 'selected' : '' ?>>Tai nạn</option>
                        <option value="congestion" <?= ($old['alert_type'] ?? '') === 'congestion' ? 'selected' : '' ?>>Ùn tắc</option>
                        <option value="construction" <?= ($old['alert_type'] ?? '') === 'construction' ? 'selected' : '' ?>>Công trình</option>
                        <option value="weather" <?= ($old['alert_type'] ?? '') === 'weather' ? 'selected' : '' ?>>Thời tiết</option>
                        <option value="other" <?= ($old['alert_type'] ?? 'other') === 'other' ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Nội dung</label>
                    <textarea name="content" class="form-control" rows="5"><?= htmlspecialchars($old['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Ngày hết hạn</label>
                    <input type="datetime-local" name="expires_at" class="form-control"
                           value="<?= htmlspecialchars($old['expires_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <small class="text-muted">Để trống nếu không có hạn</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Đang hiển thị</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="/admin/alerts" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
