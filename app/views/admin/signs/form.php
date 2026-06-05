<?php
// ==================================================================================
// VIEW: Form Thêm/Sửa biển báo giao thông (Admin)
// Form có upload hình ảnh biển báo, chọn nhóm biển báo từ danh sách $groups.
// Khi sửa: hiển thị ảnh cũ nếu có.
// Dữ liệu: $title, $sign (khi sửa), $groups (danh sách nhóm biển báo).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;

// Xác định chế độ: isset($sign) => đang sửa
$isEdit = isset($sign);
$old = $isEdit ? $sign : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<!-- Tiêu đề trang; mb-4: margin-bottom 1.5rem -->
<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <!-- enctype="multipart/form-data": bắt buộc để upload ảnh biển báo -->
        <form method="POST" action="<?= $isEdit ? '/admin/signs/' . $sign['id'] : '/admin/signs' ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <!-- row g-3: hàng flex, khoảng cách cột 1rem -->
            <div class="row g-3">
                <!-- ===== Mã biển báo (col-md-4): VD: P.101, P.102 ===== -->
                <div class="col-md-4">
                    <label class="form-label">Sign Code <span class="text-danger">*</span></label>
                    <input type="text" name="sign_code" class="form-control <?= isset($errors['sign_code']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['sign_code'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g., P.101">
                    <?php if (isset($errors['sign_code'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['sign_code'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Tên biển báo (col-md-8) ===== -->
                <div class="col-md-8">
                    <label class="form-label">Sign Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Nhóm biển báo (col-md-6): select từ danh sách groups ===== -->
                <div class="col-md-6">
                    <label class="form-label">Sign Group</label>
                    <select name="group_id" class="form-select <?= isset($errors['group_id']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Select group --</option>
                        <?php foreach ($groups as $g): ?>
                            <!-- Hiển thị: Tên nhóm (Prefix), VD: Biển báo cấm (P) -->
                            <option value="<?= $g['id'] ?>" <?= ($old['group_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($g['name'], ENT_QUOTES, 'UTF-8') ?> (<?= htmlspecialchars($g['sign_prefix'], ENT_QUOTES, 'UTF-8') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ===== Hình ảnh (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Image</label>
                    <!-- accept="image/*": chỉ chấp nhận file ảnh -->
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <?php if ($isEdit && !empty($sign['image'])): ?>
                        <!-- Khi sửa và có ảnh cũ: hiển thị ảnh hiện tại -->
                        <!-- mt-1: margin-top 0.25rem; rounded: bo góc; max-height:80px: giới hạn chiều cao -->
                        <div class="mt-1">
                            <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-height:80px" class="rounded">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ===== Mô tả (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <!-- rows="4": textarea cao 4 dòng -->
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <!-- ===== Nút Lưu và Quay lại ===== -->
            <!-- mt-4: margin-top 1.5rem -->
            <div class="mt-4">
                <!-- btn-primary: nút xanh; fa-save: icon đĩa mềm lưu; me-1: margin-right 0.25rem -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                <!-- btn-secondary: nút xám; fa-arrow-left: icon mũi tên trái quay lại -->
                <a href="/admin/signs" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </form>
    </div>
</div>
