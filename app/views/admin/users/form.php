<?php
// ==================================================================================
// VIEW: Form Thêm/Sửa người dùng (Admin)
// Dùng chung cho cả 2 chế độ: thêm mới ($isEdit=false) và chỉnh sửa ($isEdit=true).
// Khi sửa: $user chứa dữ liệu người dùng hiện tại.
// Khi thêm: dùng lại old_input từ session nếu form bị lỗi validation.
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;

// Xác định chế độ: isset($user) => đang sửa người dùng
$isEdit = isset($user);
// Nếu đang sửa: lấy dữ liệu từ $user; nếu thêm mới: lấy old_input từ session (khi có lỗi validation)
$old = $isEdit ? $user : (Session::get('old_input') ?? []);
// Lấy danh sách lỗi validation từ session (nếu có)
$errors = Session::get('form_errors') ?? [];
// Xóa lỗi và old_input khỏi session sau khi đã đọc, tránh hiển thị lại ở request sau
Session::remove('form_errors');
Session::remove('old_input');
?>

<!-- Tiêu đề trang (đã escape XSS) — class="mb-4": margin-bottom 1.5rem -->
<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<!-- card: thẻ chứa form -->
<div class="card">
    <!-- card-body: nội dung thẻ (có padding mặc định) -->
    <div class="card-body">
        <!-- Form POST: nếu sửa thì action đến /admin/users/{id}, nếu thêm thì action đến /admin/users -->
        <form method="POST" action="<?= $isEdit ? '/admin/users/' . $user['id'] : '/admin/users' ?>">
            <!-- CSRF token chống tấn công CSRF -->
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <!-- row g-3: hàng flex với khoảng cách giữa các cột là 1rem (gutters) -->
            <div class="row g-3">
                <!-- ===== Họ tên (col-md-6: chiếm nửa dòng trên desktop) ===== -->
                <div class="col-md-6">
                    <!-- form-label: nhãn input Bootstrap; text-danger: chữ đỏ cho dấu * bắt buộc -->
                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <!-- form-control: input Bootstrap; is-invalid: thêm viền đỏ nếu có lỗi validation -->
                    <input type="text" name="fullname" class="form-control <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['fullname'])): ?>
                        <!-- invalid-feedback: chữ đỏ hiển thị lỗi validation bên dưới input -->
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['fullname'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Email (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['email'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Số điện thoại (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control <?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['phone'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Mật khẩu (col-md-6) ===== -->
                <div class="col-md-6">
                    <!-- Khi sửa: không bắt buộc mật khẩu (để trống = giữ nguyên) -->
                    <label class="form-label">Mật khẩu <?= $isEdit ? '' : '<span class="text-danger">*</span>' ?></label>
                    <input type="password" name="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                           placeholder="<?= $isEdit ? 'Để trống nếu không đổi' : '' ?>">
                    <?php if (isset($errors['password'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['password'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Vai trò (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Vai trò</label>
                    <!-- form-select: dropdown select Bootstrap -->
                    <select name="role" class="form-select">
                        <option value="user" <?= ($old['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                        <option value="admin" <?= ($old['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <!-- ===== Trạng thái (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <!-- (int) để so sánh chính xác, mặc định là 1 (hoạt động) -->
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Hoạt động</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Bị khóa</option>
                    </select>
                </div>
            </div>

            <!-- ===== Nút Lưu và Quay lại ===== -->
            <!-- mt-4: margin-top 1.5rem -->
            <div class="mt-4">
                <!-- btn-primary: nút xanh; fa-save: icon đĩa mềm (lưu); me-1: margin-right 0.25rem -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <!-- btn-secondary: nút xám; fa-arrow-left: icon mũi tên trái (quay lại) -->
                <a href="/admin/users" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
