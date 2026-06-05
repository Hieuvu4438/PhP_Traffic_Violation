<?php use App\Core\Session; ?>

<!--
  Trang đăng ký tài khoản người dùng (client/auth/register.php)
  - Form gồm: họ tên, email, số điện thoại, mật khẩu, xác nhận mật khẩu
  - Mật khẩu yêu cầu tối thiểu 6 ký tự (minlength="6")
  - novalidate để tắt validation trình duyệt mặc định -> dùng server-side Validator
  - CSRF token bảo vệ form POST
-->

<!-- container: căn giữa | py-5: padding top/bottom 3rem -->
<div class="container py-5">
    <!-- row justify-content-center: căn giữa cột theo chiều ngang -->
    <div class="row justify-content-center">
        <!-- col-md-6: 6/12 trên tablet | col-lg-5: 5/12 trên desktop -->
        <div class="col-md-6 col-lg-5">
            <!-- card shadow-sm: hộp bo góc đổ bóng nhẹ -->
            <div class="card shadow-sm">
                <!-- p-4: padding 1.5rem bên trong -->
                <div class="card-body p-4">
                    <!-- text-center: căn giữa | mb-4: margin-bottom 1.5rem | fw-bold: in đậm -->
                    <h3 class="card-title text-center mb-4 fw-bold">
                        <!-- fa-user-plus: icon người + dấu cộng (đăng ký tài khoản mới) -->
                        <i class="fas fa-user-plus me-2 text-primary"></i>Register Account
                    </h3>

                    <!-- Partial hiển thị thông báo validation / kết quả từ session -->
                    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

                    <!-- novalidate: tắt HTML5 validation, dùng server-side Validator class -->
                    <form method="POST" action="/dang-ky" novalidate>
                        <!-- CSRF token chống giả mạo request -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <!-- Họ và tên -->
                        <div class="mb-3">
                            <!-- text-danger: dấu sao đỏ đánh dấu bắt buộc -->
                            <label for="fullname" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <!-- fa-user: icon người -->
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="fullname" name="fullname"
                                       placeholder="Enter your full name" required autofocus>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <!-- fa-envelope: icon phong bì thư (email) -->
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Enter your email address" required>
                            </div>
                        </div>

                        <!-- Số điện thoại -->
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <!-- fa-phone: icon điện thoại -->
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <!-- type="tel" để mobile hiển thị bàn phím số -->
                                <input type="tel" class="form-control" id="phone" name="phone"
                                       placeholder="e.g. 0912345678" required>
                            </div>
                        </div>

                        <!-- Mật khẩu -->
                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <!-- fa-lock: icon khóa -->
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <!-- minlength="6": mật khẩu tối thiểu 6 ký tự -->
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Minimum 6 characters" required minlength="6">
                            </div>
                        </div>

                        <!-- Xác nhận mật khẩu -->
                        <div class="mb-4">
                            <label for="password_confirm" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password_confirm"
                                       name="password_confirm" placeholder="Re-enter your password" required>
                            </div>
                        </div>

                        <!-- d-grid: nút giãn full width -->
                        <div class="d-grid mb-3">
                            <!-- btn-lg: nút kích thước lớn -->
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </button>
                        </div>

                        <!-- Link chuyển đến trang đăng nhập nếu đã có tài khoản -->
                        <div class="text-center">
                            <p class="mb-0">Already have an account?
                                <a href="/dang-nhap" class="fw-bold text-decoration-none">Login</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-3">
                <a href="/" class="text-muted text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
