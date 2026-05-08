<?php
require __DIR__ . '/../../partials/alerts.php';
use App\Core\Session;
$isEdit = isset($location);
$old = $isEdit ? $location : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '/admin/locations/' . $location['id'] : '/admin/locations' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tên địa điểm <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loại <span class="text-danger">*</span></label>
                    <select name="type" class="form-select <?= isset($errors['type']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Chọn loại --</option>
                        <option value="camera" <?= ($old['type'] ?? '') === 'camera' ? 'selected' : '' ?>>Camera</option>
                        <option value="csgt" <?= ($old['type'] ?? '') === 'csgt' ? 'selected' : '' ?>>CSGT</option>
                        <option value="toll" <?= ($old['type'] ?? '') === 'toll' ? 'selected' : '' ?>>Trạm thu phí</option>
                        <option value="inspection" <?= ($old['type'] ?? '') === 'inspection' ? 'selected' : '' ?>>Đăng kiểm</option>
                    </select>
                    <?php if (isset($errors['type'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['type'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Địa chỉ</label>
                    <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vĩ độ (Latitude)</label>
                    <input type="text" name="latitude" class="form-control <?= isset($errors['latitude']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars((string)($old['latitude'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" placeholder="VD: 21.0278">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Kinh độ (Longitude)</label>
                    <input type="text" name="longitude" class="form-control <?= isset($errors['longitude']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars((string)($old['longitude'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" placeholder="VD: 105.8342">
                </div>
                <div class="col-12">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="/admin/locations" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
