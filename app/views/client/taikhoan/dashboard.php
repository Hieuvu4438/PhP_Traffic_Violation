<?php
use App\Core\Session;
use App\Core\Helper;
?>

<!--
  Trang dashboard tài khoản (client/taikhoan/dashboard.php)
  - Welcome card: chào tên người dùng từ Session
  - 3 thẻ thống kê: phương tiện đã đăng ký, tra cứu gần đây, hồ sơ
  - Quick links: 4 nút điều hướng chính
  - Lịch sử tra cứu gần đây: bảng table-responsive
  - $vehicleCount: tổng số phương tiện
  - $recentHistory: danh sách tra cứu gần nhất
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-tachometer-alt: icon đồng hồ tốc độ (bảng điều khiển) -->
        <i class="fas fa-tachometer-alt me-2 text-primary"></i>Tài khoản của tôi
    </h2>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- row g-4: grid gap 1.5rem -->
    <div class="row g-4">
        <!-- === Welcome Card === -->
        <div class="col-12">
            <!-- bg-primary bg-opacity-10: nền xanh trong suốt 10% -->
            <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <!-- Session::get('user_name'): tên người dùng từ session đăng nhập -->
                    <h4 class="mb-1">Xin chào, <?= htmlspecialchars(Session::get('user_name'), ENT_QUOTES, 'UTF-8') ?>!</h4>
                    <p class="text-muted mb-0">Chào mừng bạn đến với trang quản lý tài khoản.</p>
                </div>
            </div>
        </div>

        <!-- === Thẻ thống kê 1: Phương tiện === -->
        <!-- col-md-4: 4/12 desktop, full mobile -->
        <div class="col-md-4">
            <!-- text-center h-100: căn giữa nội dung, full chiều cao -->
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <!-- fa-car fa-3x: icon xe ô tô kích thước 3x -->
                        <i class="fas fa-car fa-3x"></i>
                    </div>
                    <!-- number_format: định dạng số có dấu phẩy -->
                    <h3 class="fw-bold mb-0"><?= number_format($vehicleCount ?? 0) ?></h3>
                    <p class="text-muted mb-3">Phương tiện đã đăng ký</p>
                    <!-- btn-outline-primary btn-sm: nút viền xanh, nhỏ -->
                    <a href="/tai-khoan/phuong-tien" class="btn btn-outline-primary btn-sm">
                        <!-- fa-cog: icon bánh răng (quản lý) -->
                        <i class="fas fa-cog me-1"></i>Quản lý
                    </a>
                </div>
            </div>
        </div>

        <!-- === Thẻ thống kê 2: Tra cứu gần đây === -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <!-- fa-history: icon đồng hồ lịch sử -->
                        <i class="fas fa-history fa-3x"></i>
                    </div>
                    <h3 class="fw-bold mb-0"><?= number_format(count($recentHistory ?? [])) ?></h3>
                    <p class="text-muted mb-3">Tra cứu gần đây</p>
                    <a href="/tai-khoan/lich-su" class="btn btn-outline-info btn-sm">
                        <!-- fa-list: icon danh sách -->
                        <i class="fas fa-list me-1"></i>Xem tất cả
                    </a>
                </div>
            </div>
        </div>

        <!-- === Thẻ thống kê 3: Hồ sơ === -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <!-- fa-user-circle: icon người trong vòng tròn (hồ sơ) -->
                        <i class="fas fa-user-circle fa-3x"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Hồ sơ</h3>
                    <p class="text-muted mb-3">Thông tin cá nhân</p>
                    <a href="/tai-khoan/ho-so" class="btn btn-outline-success btn-sm">
                        <!-- fa-edit: icon bút chì (chỉnh sửa) -->
                        <i class="fas fa-edit me-1"></i>Cập nhật
                    </a>
                </div>
            </div>
        </div>

        <!-- === Quick Links === -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-link: icon mắt xích liên kết -->
                    <i class="fas fa-link me-2"></i>Truy cập nhanh
                </div>
                <div class="card-body">
                    <!-- row g-2: grid gap 0.5rem |
                         col-md-3 col-6: 4 cột desktop, 2 cột mobile -->
                    <div class="row g-2">
                        <div class="col-md-3 col-6">
                            <a href="/tai-khoan/phuong-tien" class="btn btn-outline-primary btn-sm w-100">
                                <i class="fas fa-car me-1"></i>Phương tiện
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/tai-khoan/lich-su" class="btn btn-outline-info btn-sm w-100">
                                <i class="fas fa-history me-1"></i>Lịch sử
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/tai-khoan/ho-so" class="btn btn-outline-success btn-sm w-100">
                                <!-- fa-user: icon người dùng -->
                                <i class="fas fa-user me-1"></i>Hồ sơ
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <!-- btn-outline-warning: nút viền cam -->
                            <a href="/tra-cuu" class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-search me-1"></i>Tra cứu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- === Lịch sử tra cứu gần đây === -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-history me-2"></i>Lịch sử tra cứu gần đây
                </div>
                <!-- p-0: không padding để bảng sát viền -->
                <div class="card-body p-0">
                    <?php if (empty($recentHistory)): ?>
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Bạn chưa thực hiện tra cứu nào.</p>
                            <a href="/tra-cuu" class="btn btn-outline-primary btn-sm mt-2">Tra cứu ngay</a>
                        </div>
                    <?php else: ?>
                        <!-- table-responsive: cuộn ngang trên mobile -->
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Biển số xe</th>
                                        <th>Loại xe</th>
                                        <th class="text-center">Kết quả</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recentHistory as $item): ?>
                                        <tr>
                                            <td>
                                                <!-- Helper::formatDateTime: định dạng ngày giờ đầy đủ -->
                                                <?= htmlspecialchars(Helper::formatDateTime($item['searched_at']), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($item['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            </td>
                                            <td>
                                                <!-- match expression PHP 8.x: map vehicle_type -> badge -->
                                                <?php
                                                echo match($item['vehicle_type']) {
                                                    'car' => '<span class="badge bg-primary">Ô tô</span>',
                                                    'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
                                                    'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
                                                    default => htmlspecialchars($item['vehicle_type'], ENT_QUOTES, 'UTF-8'),
                                                };
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <!-- Nếu có vi phạm: badge đỏ | Không: badge xanh lá -->
                                                <?php if ((int) $item['result_count'] > 0): ?>
                                                    <span class="badge bg-danger"><?= $item['result_count'] ?> vi phạm</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Không vi phạm</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
