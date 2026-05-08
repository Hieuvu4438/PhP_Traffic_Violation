<?php use App\Core\Session; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4 fw-bold">
                        <i class="fas fa-user-plus me-2 text-primary"></i>Đăng ký tài khoản
                    </h3>

                    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

                    <form method="POST" action="/dang-ky" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <div class="mb-3">
                            <label for="fullname" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                       placeholder="Nhập họ và tên" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Nhập địa chỉ email" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       placeholder="VD: 0912345678" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Tối thiểu 6 ký tự" required minlength="6">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirm"
                                       name="password_confirm" placeholder="Nhập lại mật khẩu" required>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Đăng ký
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Đã có tài khoản?
                                <a href="/dang-nhap" class="fw-bold text-decoration-none">Đăng nhập</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="/" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Quay về trang chủ
                </a>
            </div>
        </div>
    </div>
</div>
