<?php use App\Core\Session; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h3 class="card-title text-center mb-4 fw-bold">
                        <i class="fas fa-sign-in-alt me-2 text-primary"></i>Đăng nhập
                    </h3>

                    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

                    <form method="POST" action="/dang-nhap">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Nhập địa chỉ email" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Mật khẩu</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Nhập mật khẩu" required>
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
                            </button>
                        </div>

                        <div class="text-center">
                            <p class="mb-0">Chưa có tài khoản?
                                <a href="/dang-ky" class="fw-bold text-decoration-none">Đăng ký ngay</a>
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
