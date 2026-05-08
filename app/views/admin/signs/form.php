<?php
require __DIR__ . '/../../partials/alerts.php';
use App\Core\Session;
$isEdit = isset($sign);
$old = $isEdit ? $sign : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '/admin/signs/' . $sign['id'] : '/admin/signs' ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Mã biển báo <span class="text-danger">*</span></label>
                    <input type="text" name="sign_code" class="form-control <?= isset($errors['sign_code']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['sign_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="VD: P.101">
                    <?php if (isset($errors['sign_code'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['sign_code'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Tên biển báo <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nhóm biển báo</label>
                    <select name="group_id" class="form-select <?= isset($errors['group_id']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Chọn nhóm --</option>
                        <?php foreach ($groups as $g): ?>
                            <option value="<?= $g['id'] ?>" <?= ($old['group_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($g['sign_prefix'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hình ảnh</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if ($isEdit && !empty($sign['image'])): ?>
                        <div class="mt-1">
                            <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-height:80px" class="rounded">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="/admin/signs" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
