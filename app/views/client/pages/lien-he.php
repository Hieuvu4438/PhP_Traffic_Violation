<?php use App\Core\Session; ?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-envelope me-2 text-primary"></i>Liên hệ
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row g-4">
        <!-- Contact Form -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4">
                        <i class="fas fa-paper-plane me-2 text-primary"></i>Gửi tin nhắn cho chúng tôi
                    </h5>
                    <form method="POST" action="/lien-he" novalidate>
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" class="form-control" id="name" name="name"
                                           placeholder="Nhập họ và tên của bạn" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                    <input type="email" class="form-control" id="email" name="email"
                                           placeholder="Nhập địa chỉ email" required>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                   placeholder="Nhập tiêu đề tin nhắn" required>
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6"
                                      placeholder="Nhập nội dung tin nhắn của bạn..." required></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>Gửi tin nhắn
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Contact Info -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-info-circle me-2"></i>Thông tin liên hệ
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
                                    <i class="fas fa-map-marker-alt text-primary fa-lg"></i>
                                </div>
                                <div>
                                    <strong>Địa chỉ</strong>
                                    <p class="mb-0 text-muted small">Hà Nội, Việt Nam</p>
                                </div>
                            </div>
                        </li>
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
                        <li>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 p-3 rounded me-3">
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

            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-question-circle me-2"></i>Bạn cần hỗ trợ?
                </div>
                <div class="card-body">
                    <p class="text-muted small">Xem các câu hỏi thường gặp trước khi gửi tin nhắn.</p>
                    <a href="/faq" class="btn btn-outline-info btn-sm w-100">
                        <i class="fas fa-question-circle me-2"></i>Xem FAQ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
