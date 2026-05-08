<?php use App\Core\Session; ?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-user-edit me-2 text-primary"></i>Hồ sơ cá nhân
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <a href="/tai-khoan" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i>Quay lại tài khoản
    </a>

    <div class="row g-4">
        <!-- Profile Form -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-user me-2"></i>Thông tin cá nhân
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="/tai-khoan/ho-so">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                       value="<?= htmlspecialchars($user['fullname'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       value="<?= htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                       required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Vai trò</label>
                            <input type="text" class="form-control bg-light"
                                   value="<?= ($user['role'] ?? '') === 'admin' ? 'Quản trị viên' : 'Người dùng' ?>"
                                   readonly disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ngày tham gia</label>
                            <input type="text" class="form-control bg-light"
                                   value="<?= htmlspecialchars((new DateTime($user['created_at'] ?? 'now'))->format('d/m/Y H:i'), ENT_QUOTES, 'UTF-8') ?>"
                                   readonly disabled>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Cập nhật hồ sơ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change Password -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-lock me-2"></i>Đổi mật khẩu
                </div>
                <div class="card-body">
                    <form method="POST" action="/tai-khoan/doi-mat-khau">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <div class="mb-3">
                            <label for="old_password" class="form-label">Mật khẩu hiện tại</label>
                            <input type="password" class="form-control" id="old_password" name="old_password"
                                   placeholder="Nhập mật khẩu hiện tại" required>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Mật khẩu mới</label>
                            <input type="password" class="form-control" id="new_password" name="new_password"
                                   placeholder="Tối thiểu 6 ký tự" required minlength="6">
                        </div>

                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" id="password_confirm"
                                   name="password_confirm" placeholder="Nhập lại mật khẩu mới" required>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Đổi mật khẩu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
