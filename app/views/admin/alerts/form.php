<?php
// ==================================================================================
// VIEW: Form Thêm/Sửa cảnh báo giao thông (Admin)
// Form gồm: tiêu đề, loại cảnh báo (select: tai nạn/ùn tắc/công trình/thời tiết/khác),
// nội dung, ngày hết hạn (có thể để trống), trạng thái.
// Dữ liệu: $title, $alert (khi sửa).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;

// Xác định chế độ: isset($alert) => đang sửa
$isEdit = isset($alert);
$old = $isEdit ? $alert : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<!-- Tiêu đề trang; mb-4: margin-bottom 1.5rem -->
<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <!-- Form POST: nếu sửa thì đến /admin/alerts/{id}, thêm mới thì đến /admin/alerts -->
        <form method="POST" action="<?= $isEdit ? '/admin/alerts/' . $alert['id'] : '/admin/alerts' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <!-- row g-3: hàng flex, khoảng cách cột 1rem -->
            <div class="row g-3">
                <!-- ===== Tiêu đề (col-md-8) ===== -->
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['title'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Loại cảnh báo (col-md-4) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Alert Type</label>
                    <select name="alert_type" class="form-select">
                        <option value="accident" <?= ($old['alert_type'] ?? 'other') === 'accident' ? 'selected' : '' ?>>Accident</option>
                        <option value="congestion" <?= ($old['alert_type'] ?? '') === 'congestion' ? 'selected' : '' ?>>Congestion</option>
                        <option value="construction" <?= ($old['alert_type'] ?? '') === 'construction' ? 'selected' : '' ?>>Construction</option>
                        <option value="weather" <?= ($old['alert_type'] ?? '') === 'weather' ? 'selected' : '' ?>>Weather</option>
                        <!-- Mặc định là 'other' nếu không chọn -->
                        <option value="other" <?= ($old['alert_type'] ?? 'other') === 'other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <!-- ===== Nội dung (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Content</label>
                    <!-- rows="5": textarea cao 5 dòng -->
                    <textarea name="content" class="form-control" rows="5"><?= htmlspecialchars($old['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>

                <!-- ===== Ngày hết hạn (col-md-4) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Expiration Date</label>
                    <!-- type="datetime-local": input chọn ngày + giờ -->
                    <input type="datetime-local" name="expires_at" class="form-control"
                           value="<?= htmlspecialchars($old['expires_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <!-- text-muted: chữ xám, hướng dẫn có thể để trống -->
                    <small class="text-muted">Leave blank if no expiration</small>
                </div>

                <!-- ===== Trạng thái (col-md-4) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <!-- Mặc định: 1 = Đang hiển thị -->
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Active</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Hidden</option>
                    </select>
                </div>
            </div>

            <!-- ===== Nút Lưu và Quay lại ===== -->
            <!-- mt-4: margin-top 1.5rem -->
            <div class="mt-4">
                <!-- btn-primary: nút xanh; fa-save: icon đĩa mềm lưu; me-1: margin-right 0.25rem -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                <!-- btn-secondary: nút xám; fa-arrow-left: icon mũi tên trái quay lại -->
                <a href="/admin/alerts" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </form>
    </div>
</div>
