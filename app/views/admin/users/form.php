<?php
require __DIR__ . '/../../partials/alerts.php';
use App\Core\Session;
$isEdit = isset($user);
$old = $isEdit ? $user : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '/admin/users/' . $user['id'] : '/admin/users' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text" name="fullname" class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['fullname'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['fullname'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mật khẩu <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?></label>
                    <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           placeholder="<?= $isEdit ? 'Để trống nếu không đổi' : '' ?>">
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vai trò</label>
                    <select name="role" class="form-select">
                        <option value="user" <?= ($old['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="/admin/users" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
