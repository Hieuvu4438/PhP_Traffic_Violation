<?php
use App\Core\Session;
use App\Core\Helper;
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-tachometer-alt me-2 text-primary"></i>Tài khoản của tôi
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row g-4">
        <!-- Welcome Card -->
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-primary bg-opacity-10">
                <div class="card-body">
                    <h4 class="mb-1">Xin chào, <?= htmlspecialchars(Session::get('user_name'), ENT_QUOTES, 'UTF-8') ?>!</h4>
                    <p class="text-muted mb-0">Chào mừng bạn đến với trang quản lý tài khoản.</p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <div class="text-primary mb-2">
                        <i class="fas fa-car fa-3x"></i>
                    </div>
                    <h3 class="fw-bold mb-0"><?= number_format($vehicleCount ?? 0) ?></h3>
                    <p class="text-muted mb-3">Phương tiện đã đăng ký</p>
                    <a href="/tai-khoan/phuong-tien" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-cog me-1"></i>Quản lý
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <div class="text-info mb-2">
                        <i class="fas fa-history fa-3x"></i>
                    </div>
                    <h3 class="fw-bold mb-0"><?= number_format(count($recentHistory ?? [])) ?></h3>
                    <p class="text-muted mb-3">Tra cứu gần đây</p>
                    <a href="/tai-khoan/lich-su" class="btn btn-outline-info btn-sm">
                        <i class="fas fa-list me-1"></i>Xem tất cả
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <div class="text-success mb-2">
                        <i class="fas fa-user-circle fa-3x"></i>
                    </div>
                    <h3 class="fw-bold mb-0">Hồ sơ</h3>
                    <p class="text-muted mb-3">Thông tin cá nhân</p>
                    <a href="/tai-khoan/ho-so" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-edit me-1"></i>Cập nhật
                    </a>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-link me-2"></i>Truy cập nhanh
                </div>
                <div class="card-body">
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
                                <i class="fas fa-user me-1"></i>Hồ sơ
                            </a>
                        </div>
                        <div class="col-md-3 col-6">
                            <a href="/tra-cuu" class="btn btn-outline-warning btn-sm w-100">
                                <i class="fas fa-search me-1"></i>Tra cứu
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Search History -->
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-history me-2"></i>Lịch sử tra cứu gần đây
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentHistory)): ?>
                        <div class="text-center py-4 text-muted">
                            <p class="mb-0">Bạn chưa thực hiện tra cứu nào.</p>
                            <a href="/tra-cuu" class="btn btn-outline-primary btn-sm mt-2">Tra cứu ngay</a>
                        </div>
                    <?php else: ?>
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
                                                <?= htmlspecialchars(Helper::formatDateTime($item['searched_at']), ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td>
                                                <strong><?= htmlspecialchars($item['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong>
                                            </td>
                                            <td>
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
