<!--
  Trang giới thiệu (client/pages/gioi-thieu.php)
  - Cột trái: nội dung giới thiệu về website + danh sách tính năng chính
  - Cột phải: sidebar Lưu ý (các điểm cần biết) + Liên kết hữu ích
  - Trang tĩnh, không có form hay dữ liệu động
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-info-circle: icon thông tin -->
        <i class="fas fa-info-circle me-2 text-primary"></i>About
    </h2>

    <!-- row g-4: grid gap 1.5rem -->
    <div class="row g-4">
        <!-- === Cột trái: Nội dung chính (8/12 desktop) === -->
        <div class="col-lg-8">
            <!-- Card: Về chúng tôi + Tính năng -->
            <div class="card shadow-sm border-0 mb-4">
                <!-- p-4: padding 1.5rem -->
                <div class="card-body p-4">
                    <h3 class="fw-bold mb-3">About Us</h3>
                    <p>
                        <strong>Traffic Violation Lookup</strong> is a website that provides online traffic violation lookup services,
                        helping citizens easily check traffic violations of vehicles through license plate numbers.
                    </p>
                    <p>
                        Data is compiled from the Traffic Police Department and the Vietnam Registry, ensuring
                        accuracy and regular updates.
                    </p>

                    <!-- Tính năng chính: lưới 2 cột -->
                    <h4 class="fw-bold mt-4 mb-3">Key Features</h4>
                    <div class="row g-3">
                        <!-- Tra cứu phạt nguội -->
                        <div class="col-md-6">
                            <!-- d-flex align-items-start gap-2: flexbox, icon + text, gap 0.5rem -->
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-search text-primary mt-1 fa-fw: icon tìm kiếm xanh, căn đều width -->
                                <i class="fas fa-search text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Traffic Violation Lookup</strong>
                                    <p class="text-muted mb-0 small">Quick search by license plate and vehicle type.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Tin tức giao thông -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-newspaper: icon tờ báo -->
                                <i class="fas fa-newspaper text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Traffic News</strong>
                                    <p class="text-muted mb-0 small">Latest traffic news updates.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Tra cứu biển báo -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-sign: icon biển báo -->
                                <i class="fas fa-sign text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Traffic Sign Lookup</strong>
                                    <p class="text-muted mb-0 small">Browse and search the traffic sign system.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Thống kê -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-chart-bar: icon biểu đồ cột -->
                                <i class="fas fa-chart-bar text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Visual Statistics</strong>
                                    <p class="text-muted mb-0 small">Traffic violation statistics charts.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Bản đồ -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-map-location-dot: icon bản đồ có đánh dấu -->
                                <i class="fas fa-map-location-dot text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Location Map</strong>
                                    <p class="text-muted mb-0 small">View camera, traffic police, and toll station locations.</p>
                                </div>
                            </div>
                        </div>
                        <!-- Quản lý tài khoản -->
                        <div class="col-md-6">
                            <div class="d-flex align-items-start gap-2 mb-3">
                                <!-- fa-user: icon người dùng -->
                                <i class="fas fa-user text-primary mt-1 fa-fw"></i>
                                <div>
                                    <strong>Account Management</strong>
                                    <p class="text-muted mb-0 small">Register to manage vehicles and search history.</p>
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
                    <i class="fas fa-exclamation-circle me-2"></i>Important Notes
                </div>
                <div class="card-body">
                    <!-- small: chữ nhỏ -->
                    <ul class="mb-0 small">
                        <li class="mb-2">Lookup data is for reference only.</li>
                        <li class="mb-2">Results may not include the latest violations.</li>
                        <li class="mb-2">For accurate confirmation, please contact your local traffic police.</li>
                        <li class="mb-2">This website was built for academic purposes.</li>
                    </ul>
                </div>
            </div>

            <!-- Card: Liên kết hữu ích -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <!-- fa-link: icon mắt xích (liên kết) -->
                    <i class="fas fa-link me-2"></i>Useful Links
                </div>
                <div class="card-body">
                    <!-- d-grid gap-2: các nút xếp dọc, gap 0.5rem -->
                    <div class="d-grid gap-2">
                        <!-- btn-outline-primary: nút viền xanh -->
                        <a href="/tra-cuu" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-search me-2"></i>Search Violations
                        </a>
                        <!-- btn-outline-info: nút viền xanh nhạt -->
                        <a href="/tin-tuc" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-newspaper me-2"></i>Traffic News
                        </a>
                        <!-- btn-outline-secondary: nút viền xám -->
                        <a href="/bien-bao" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-sign me-2"></i>Traffic Signs
                        </a>
                        <!-- btn-outline-success: nút viền xanh lá -->
                        <a href="/thong-ke" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-chart-bar me-2"></i>Statistics
                        </a>
                        <!-- btn-outline-warning: nút viền vàng -->
                        <a href="/faq" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-question-circle me-2"></i>FAQ
                        </a>
                        <!-- btn-outline-danger: nút viền đỏ -->
                        <a href="/lien-he" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-envelope me-2"></i>Contact
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
