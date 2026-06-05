<?php use App\Core\Session; ?>

<!--
  Trang đăng nhập người dùng (client/auth/login.php)
  - Hiển thị form đăng nhập với email và mật khẩu
  - Có CSRF token để chống tấn công giả mạo (Session::csrfToken)
  - Link chuyển hướng đến trang đăng ký nếu chưa có tài khoản
  - Bootstrap 5 card trung tâm, responsive với col-md-5 col-lg-4
-->

<!-- container: căn giữa nội dung với padding top/bottom 3rem (py-5) -->
<div class="container py-5">
    <!-- row justify-content-center: căn giữa cột theo chiều ngang -->
    <div class="row justify-content-center">
        <!-- col-md-5: 5/12 trên tablet | col-lg-4: 4/12 trên desktop -->
        <div class="col-md-5 col-lg-4">
            <!-- card shadow-sm: hộp bo góc + đổ bóng nhẹ -->
            <div class="card shadow-sm">
                <!-- card-body p-4: padding 1.5rem bên trong -->
                <div class="card-body p-4">
                    <!-- text-center: căn giữa | mb-4: margin-bottom 1.5rem | fw-bold: in đậm -->
                    <h3 class="card-title text-center mb-4 fw-bold">
                        <!-- fa-sign-in-alt: icon mũi tên vào cửa (đăng nhập) | text-primary: màu xanh chủ đạo -->
                        <i class="fas fa-sign-in-alt me-2 text-primary"></i>Login
                    </h3>

                    <!-- Nhúng partial hiển thị thông báo lỗi/thành công từ session -->
                    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

                    <!-- Form POST đến /dang-nhap (route friendly URL) -->
                    <form method="POST" action="/dang-nhap">
                        <!-- CSRF token: chống tấn công Cross-Site Request Forgery -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <!-- mb-3: margin-bottom 1rem giữa các field -->
                        <div class="mb-3">
                            <!-- form-label: kiểu nhãn form của Bootstrap -->
                            <label for="email" class="form-label">Email</label>
                            <!-- input-group: nhóm input + icon -->
                            <div class="input-group">
                                <!-- input-group-text: vùng chứa icon của input group | fa-envelope: icon phong bì thư -->
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <!-- form-control: kiểu input Bootstrap | autofocus: tự focus khi load trang -->
                                <input type="email" class="form-control" id="email" name="email"
                                       placeholder="Enter your email address" required autofocus>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <!-- fa-lock: icon khóa (mật khẩu) -->
                                <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                <input type="password" class="form-control" id="password" name="password"
                                       placeholder="Enter your password" required>
                            </div>
                        </div>

                        <!-- d-grid: display grid -> nút giãn full width | mb-3: margin-bottom -->
                        <div class="d-grid mb-3">
                            <!-- btn btn-primary btn-lg: nút màu xanh, kích thước lớn -->
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>

                        <!-- text-center: căn giữa nội dung -->
                        <div class="text-center">
                            <p class="mb-0">Don't have an account?
                                <!-- fw-bold: in đậm | text-decoration-none: bỏ gạch chân link -->
                                <a href="/dang-ky" class="fw-bold text-decoration-none">Register now</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            <!-- text-center: căn giữa | mt-3: margin-top 1rem -->
            <div class="text-center mt-3">
                <!-- text-muted: chữ xám nhạt | text-decoration-none: bỏ gạch chân -->
                <a href="/" class="text-muted text-decoration-none">
                    <!-- fa-arrow-left: icon mũi tên sang trái (quay lại) -->
                    <i class="fas fa-arrow-left me-1"></i>Back to Home
                </a>
            </div>
        </div>
    </div>
</div>
