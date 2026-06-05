<!--
  Trang giới thiệu (client/pages/gioi-thieu.php)
  - Cột trái: nội dung giới thiệu về website + danh sách tính năng chính
  - Cột phải: sidebar Lưu ý (các điểm cần biết) + Liên kết hữu ích
  - Trang tĩnh, không có form hay dữ liệu động
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-info-circle: icon thông tin -->
        <i class="fas fa-info-circle me-2 text-primary"></i>Giới thiệu
    </h2>

    <!-- row g-4: grid gap 1.5rem -->
    <div class="row g-4">
        <!-- === Cột trái: Nội dung chính (8/12 desktop) === -->
        <div class="col-lg-8">
            <!-- Card: Về chúng tôi + Tính năng -->
            <div class="card shadow-sm border-0 mb-4">
                <!-- p-4: padding 1.5rem -->
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">Về chúng tôi</h3>
                    <p>
                        <strong>Tra Cứu Phạt Nguội</strong> là website cung cấp dịch vụ tra cứu phương tiện vi phạm
                        giao thông trực tuyến, giúp người dân dễ dàng kiểm tra các vi phạm giao thông của phương tiện
                        thông qua biển số xe.
                    </p>
                    <p>
                        Dữ liệu được tổng hợp từ Cục Cảnh sát Giao thông (CSGT) và Cục Đăng Kiểm Việt Nam, đảm bảo
                        tính chính xác và cập nhật thường xuyên.
                    </p>

                    <!-- Tính năng chính: lưới 2 cột -->
                    <h4 class="fw-bold mt-4 mb-3">Tính năng chính</h4>
                    <div class="row g-3">
                        <!-- Tra cứu phạt nguội -->
                        <div class="col-md-6">
                            <!-- d-flex align-items-start gap-2: flexbox, icon + text, gap 0.5rem -->
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-search text-primary mt-1 fa-fw: icon tìm kiếm xanh, căn đều width -->
                                <i class="fas fa-search text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Tra cứu phạt nguội</strong>
                                    <p class="text-muted mb-0 small">Tra cứu nhanh chóng bằng biển số xe và loại xe.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Tin tức giao thông -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-newspaper: icon tờ báo -->
                                <i class="fas fa-newspaper text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Tin tức giao thông</strong>
                                    <p class="text-muted mb-0 small">Cập nhật tin tức mới nhất về giao thông.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Tra cứu biển báo -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-sign: icon biển báo -->
                                <i class="fas fa-sign text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Tra cứu biển báo</strong>
                                    <p class="text-muted mb-0 small">Xem và tra cứu hệ thống biển báo giao thông.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Thống kê -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-chart-bar: icon biểu đồ cột -->
                                <i class="fas fa-chart-bar text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Thống kê trực quan</strong>
                                    <p class="text-muted mb-0 small">Biểu đồ thống kê vi phạm giao thông.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Bản đồ -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-map-location-dot: icon bản đồ có đánh dấu -->
                                <i class="fas fa-map-location-dot text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Bản đồ địa điểm</strong>
                                    <p class="text-muted mb-0 small">Xem vị trí camera, trạm CSGT, trạm thu phí.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Quản lý tài khoản -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-user: icon người dùng -->
                                <i class="fas fa-user text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Quản lý tài khoản</strong>
                                    <p class="text-muted mb-0 small">Đăng ký tài khoản để quản lý phương tiện và lịch sử tra cứu.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === Cột phải: Sidebar (4/12 desktop) === -->
        <div class="col-lg-4">
            <!-- Card: Lưu ý -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-exclamation-circle: icon dấu chấm than trong vòng tròn (cảnh báo/lưu ý) -->
                    <i class="fas fa-exclamation-circle me-2"></i>Lưu ý
                </div>
                <div class="card-body">
                    <!-- small: chữ nhỏ -->
                    <ul class="mb-0 small">
                        <li class="mb-2">Dữ liệu tra cứu chỉ mang tính chất tham khảo.</li>
                        <li class="mb-2">Kết quả có thể chưa cập nhật các vi phạm mới nhất.</li>
                        <li class="mb-2">Để xác nhận chính xác, vui lòng liên hệ cơ quan CSGT địa phương.</li>
                        <li class="mb-2">Website được xây dựng với mục đích học thuật.</li>
                    </ul>
                </div>
            </div>

            <!-- Card: Liên kết hữu ích -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <!-- fa-link: icon mắt xích (liên kết) -->
                    <i class="fas fa-link me-2"></i>Liên kết hữu ích
                </div>
                <div class="card-body">
                    <!-- d-grid gap-2: các nút xếp dọc, gap 0.5rem -->
                    <div class="d-grid gap-2">
                        <!-- btn-outline-primary: nút viền xanh -->
                        <a href="/tra-cuu" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-2"></i>Tra cứu phạt nguội
                        </a>
                        <!-- btn-outline-info: nút viền xanh nhạt -->
                        <a href="/tin-tuc" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-newspaper me-2"></i>Tin tức giao thông
                        </a>
                        <!-- btn-outline-secondary: nút viền xám -->
                        <a href="/bien-bao" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-sign me-2"></i>Biển báo giao thông
                        </a>
                        <!-- btn-outline-success: nút viền xanh lá -->
                        <a href="/thong-ke" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-chart-bar me-2"></i>Thống kê
                        </a>
                        <!-- btn-outline-warning: nút viền vàng -->
                        <a href="/faq" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-question-circle me-2"></i>Câu hỏi thường gặp
                        </a>
                        <!-- btn-outline-danger: nút viền đỏ -->
                        <a href="/lien-he" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-envelope me-2"></i>Liên hệ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
