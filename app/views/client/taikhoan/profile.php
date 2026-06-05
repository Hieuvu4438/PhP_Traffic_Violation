<?php use App\Core\Session; ?>

<!--
  Trang hồ sơ cá nhân (client/taikhoan/profile.php)
  - Cột trái: form cập nhật thông tin (họ tên, email, điện thoại, role, ngày tham gia)
  - Cột phải: form đổi mật khẩu (hiện tại, mới, xác nhận)
  - Role và ngày tham gia: readonly, disabled (không thể sửa)
  - CSRF token bảo vệ cả 2 form
  - $user: dữ liệu người dùng từ DB
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-user-edit: icon người + bút chì (chỉnh sửa hồ sơ) -->
        <i class="fas fa-user-edit me-2 text-primary"></i>Personal Profile
    </h2>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- Nút quay lại dashboard -->
    <a href="/tai-khoan" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i>Back to Account
    </a>

    <!-- row g-4: grid gap 1.5rem -->
    <div class="row g-4">
        <!-- === Cột trái: Form cập nhật thông tin (8/12 desktop) === -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <!-- card-header bg-primary text-white: header xanh chữ trắng -->
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-user: icon người dùng -->
                    <i class="fas fa-user me-2"></i>Personal Information
                </div>
                <!-- p-4: padding 1.5rem -->
                <div class="card-body p-4">
                    <!-- Form POST đến /tai-khoan/ho-so để cập nhật -->
                    <form method="POST" action="/tai-khoan/ho-so">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <!-- Họ và tên (bắt buộc) -->
                        <div class="mb-3">
                            <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <!-- value từ $user['fullname'] - giữ giá trị hiện tại -->
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                       value="<?= htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                        </div>

                        <!-- Email (bắt buộc) -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <!-- fa-envelope: icon phong bì thư -->
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                        </div>

                        <!-- Số điện thoại (bắt buộc) -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <!-- fa-phone: icon điện thoại -->
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                        </div>

                        <!-- Vai trò: readonly, không thể sửa -->
                        <!-- bg-light: nền xám nhạt -> thể hiện trường chỉ đọc -->
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control bg-light"
                                   value="<?= ($user['role'] ?? '') === 'admin' ? 'Administrator' : 'User' ?>"
                                   readonly disabled>
                        </div>

                        <!-- Ngày tham gia: readonly -->
                        <div class="mb-3">
                            <label class="form-label">Joined Date</label>
                            <input type="text" class="form-control bg-light"
                                   value="<?= htmlspecialchars((new DateTime($user['created_at'] ?? 'now'))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?>"
                                   readonly disabled>
                        </div>

                        <!-- d-grid: nút giãn full width -->
                        <div class="d-grid">
                            <!-- fa-save: icon đĩa mềm (lưu) -->
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- === Cột phải: Form đổi mật khẩu (4/12 desktop) === -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <!-- card-header bg-warning text-dark: header vàng chữ tối -->
                <div class="card-header bg-warning text-dark fw-bold">
                    <!-- fa-lock: icon khóa -->
                    <i class="fas fa-lock me-2"></i>Change Password
                </div>
                <div class="card-body">
                    <!-- Form POST đến /tai-khoan/doi-mat-khau -->
                    <form method="POST" action="/tai-khoan/doi-mat-khau">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <!-- Mật khẩu hiện tại -->
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="old_password" name="old_password"
                                   placeholder="Enter current password" required>
                        </div>

                        <!-- Mật khẩu mới | minlength="6": tối thiểu 6 ký tự -->
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                   placeholder="Minimum 6 characters" required minlength="6">
                        </div>

                        <!-- Xác nhận mật khẩu mới -->
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" id="password_confirm"
                                   name="password_confirm" placeholder="Re-enter new password" required>
                        </div>

                        <div class="d-grid">
                            <!-- btn-warning: nút vàng | fa-key: icon chìa khóa -->
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
