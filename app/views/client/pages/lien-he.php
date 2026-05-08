<?php use App\Core\Session; ?>

<!--
  Trang liên hệ (client/pages/lien-he.php)
  - Cột trái: form gửi tin nhắn (họ tên, email, tiêu đề, nội dung)
  - Cột phải: sidebar thông tin liên hệ (địa chỉ, email, điện thoại, giờ làm việc)
  - Sidebar dưới: card FAQ (hướng dẫn xem FAQ trước khi liên hệ)
  - CSRF token bảo vệ form POST
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-envelope: icon phong bì thư (liên hệ) -->
        <i class="fas fa-envelope me-2 text-primary"></i>Liên hệ
    </h2>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- row g-4: grid gap 1.5rem -->
    <div class="row g-4">
        <!-- === Cột trái: Form liên hệ (7/12 desktop) === -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <!-- p-4: padding 1.5rem -->
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">
                        <!-- fa-paper-plane: icon máy bay giấy (gửi tin nhắn) -->
                        <i class="fas fa-paper-plane me-2 text-primary"></i>Gửi tin nhắn cho chúng tôi
                    </h5>
                    <!-- novalidate: tắt HTML5 validation, dùng server-side -->
                    <form method="POST" action="/lien-he" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <!-- Hàng 2 cột: Họ tên + Email -->
                        <div class="row">
                            <!-- Họ tên: col-md-6 (nửa trái) -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Nhập họ và tên của bạn" required>
                                </div>
                            </div>
                            <!-- Email: col-md-6 (nửa phải) -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                           placeholder="Nhập địa chỉ email" required>
                                </div>
                            </div>
                        </div>

                        <!-- Tiêu đề (full width) -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                   placeholder="Nhập tiêu đề tin nhắn" required>
                        </div>

                        <!-- Nội dung: textarea 6 dòng (full width) -->
                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <!-- rows="6": chiều cao 6 dòng -->
                            <textarea class="form-control" id="message" name="message" rows="6"
                                      placeholder="Nhập nội dung tin nhắn của bạn..." required></textarea>
                        </div>

                        <!-- d-grid: nút giãn full width -->
                        <div class="d-grid">
                            <!-- btn-lg: nút kích thước lớn -->
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- === Cột phải: Thông tin liên hệ (5/12 desktop) === -->
        <div class="col-lg-5">
            <!-- Card: Thông tin liên hệ -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-info-circle: icon thông tin -->
                    <i class="fas fa-info-circle me-2"></i>Thông tin liên hệ
                </div>
                <div class="card-body">
                    <!-- list-unstyled: bỏ bullet mặc định -->
                    <ul class="list-unstyled mb-0">
                        <!-- Địa chỉ -->
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <!-- bg-primary bg-opacity-10 p-3 rounded: nền xanh nhạt 10%, padding, bo góc -->
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <!-- fa-map-marker-alt text-primary fa-lg: icon địa điểm xanh, lớn -->
                                    <i class="fas fa-map-marker-alt text-primary fa-lg"></i>
                                </div>
                                <div>
                                    <strong>Địa chỉ</strong>
                                    <p class="mb-0 text-muted small">Hà Nội, Việt Nam</p>
                                </div>
                            </div>
                        </li>
                        <!-- Email -->
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-envelope text-primary fa-lg"></i>
                                </div>
                                <div>
                                    <strong>Email</strong>
                                    <p class="mb-0 text-muted small">contact@tracuuphatnguoi.vn</p>
                                </div>
                            </div>
                        </li>
                        <!-- Điện thoại -->
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-phone text-primary fa-lg"></i>
                                </div>
                                <div>
                                    <strong>Điện thoại</strong>
                                    <p class="mb-0 text-muted small">1900 xxxx</p>
                                </div>
                            </div>
                        </li>
                        <!-- Giờ làm việc -->
                        <li>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <!-- fa-clock: icon đồng hồ -->
                                    <i class="fas fa-clock text-primary fa-lg"></i>
                                </div>
                                <div>
                                    <strong>Giờ làm việc</strong>
                                    <p class="mb-0 text-muted small">Thứ 2 - Thứ 6: 8:00 - 17:30</p>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Card: Hỗ trợ / FAQ -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <!-- fa-question-circle: icon dấu hỏi -->
                    <i class="fas fa-question-circle me-2"></i>Bạn cần hỗ trợ?
                </div>
                <div class="card-body">
                    <p class="text-muted small">Xem các câu hỏi thường gặp trước khi gửi tin nhắn.</p>
                    <!-- w-100: full width -->
                    <a href="/faq" class="btn btn-outline-info btn-sm w-100">
                        <i class="fas fa-question-circle me-2"></i>Xem FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
