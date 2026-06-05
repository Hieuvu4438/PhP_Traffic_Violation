<?php
// ==================================================================================
// VIEW: Form Thêm/Sửa địa điểm (Admin)
// Form gồm: tên địa điểm, loại (select: camera/csgt/toll/inspection), trạng thái,
// địa chỉ, tọa độ vĩ độ/kinh độ, mô tả.
// Dữ liệu: $title, $location (khi sửa).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;

// Xác định chế độ: isset($location) => đang sửa
$isEdit = isset($location);
$old = $isEdit ? $location : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<!-- Tiêu đề trang; mb-4: margin-bottom 1.5rem -->
<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <!-- Form POST: nếu sửa thì đến /admin/locations/{id}, thêm mới thì đến /admin/locations -->
        <form method="POST" action="<?= $isEdit ? '/admin/locations/' . $location['id'] : '/admin/locations' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <!-- row g-3: hàng flex, khoảng cách cột 1rem -->
            <div class="row g-3">
                <!-- ===== Tên địa điểm (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Location Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['name'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Loại địa điểm (col-md-3) ===== -->
                <div class="col-md-3">
                    <label class="form-label">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select <?= isset($errors['type']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Select type --</option>
                        <!-- Giá trị lưu DB là tiếng Anh, label hiển thị là tiếng Việt -->
                        <option value="camera" <?= ($old['type'] ?? '') === 'camera' ? 'selected' : '' ?>>Camera</option>
                        <option value="csgt" <?= ($old['type'] ?? '') === 'csgt' ? 'selected' : '' ?>>CSGT</option>
                        <option value="toll" <?= ($old['type'] ?? '') === 'toll' ? 'selected' : '' ?>>Toll Station</option>
                        <option value="inspection" <?= ($old['type'] ?? '') === 'inspection' ? 'selected' : '' ?>>Inspection</option>
                    </select>
                    <?php if (isset($errors['type'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['type'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Trạng thái (col-md-3) ===== -->
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <!-- Mặc định: 1 = Hoạt động -->
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                    </select>
                </div>

                <!-- ===== Địa chỉ (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <input type="text" name="address" class="form-control <?= isset($errors['address']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- ===== Vĩ độ - Latitude (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Latitude</label>
                    <!-- (string) cast để tránh lỗi khi giá trị là số float -->
                    <input type="text" name="latitude" class="form-control <?= isset($errors['latitude']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars((string)($old['latitude'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g., 21.0278">
                </div>

                <!-- ===== Kinh độ - Longitude (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Longitude</label>
                    <input type="text" name="longitude" class="form-control <?= isset($errors['longitude']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars((string)($old['longitude'] ?? '0'), ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g., 105.8342">
                </div>

                <!-- ===== Mô tả (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($old['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <!-- ===== Nút Lưu và Quay lại ===== -->
            <!-- mt-4: margin-top 1.5rem -->
            <div class="mt-4">
                <!-- btn-primary: nút xanh; fa-save: icon đĩa mềm lưu -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                <!-- btn-secondary: nút xám; fa-arrow-left: icon mũi tên trái quay lại -->
                <a href="/admin/locations" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </form>
    </div>
</div>
