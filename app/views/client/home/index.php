<?php
use App\Core\Session;
use App\Core\Helper;

function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => $type,
    };
}
?>

<!-- Hero Section -->
<section class="hero-section bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="fw-bold mb-3">Tra Cứu Phương Tiện Vi Phạm Giao Thông</h1>
                <p class="lead mb-4">Kiểm tra phạt nguội toàn quốc nhanh chóng, chính xác. Dữ liệu từ Cục CSGT & Cục Đăng Kiểm Việt Nam.</p>

                <div class="hero-search p-4 bg-white bg-opacity-10 rounded-3 backdrop-blur">
                    <form method="POST" action="/tra-cuu" class="row g-3">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <div class="col-md-5">
                            <label for="plate_number" class="form-label text-white">Biển số xe</label>
                            <input type="text" class="form-control form-control-lg" id="plate_number"
                                   name="plate_number" placeholder="VD: 30A-12345" required>
                        </div>

                        <div class="col-md-4">
                            <label for="vehicle_type" class="form-label text-white">Loại xe</label>
                            <select class="form-select form-select-lg" id="vehicle_type" name="vehicle_type" required>
                                <option value="">-- Chọn loại xe --</option>
                                <option value="car">Ô tô</option>
                                <option value="motorcycle">Xe máy</option>
                                <option value="electric_motorcycle">Xe máy điện</option>
                            </select>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">
                                <i class="fas fa-search me-2"></i>Tra cứu ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="col-lg-4 d-none d-lg-block text-center">
                <i class="fas fa-shield-halved" style="font-size: 12rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Stat Cards -->
<section class="py-4 border-bottom">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-primary mb-2">
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?= number_format($totalViolations ?? 0) ?></h3>
                        <p class="text-muted mb-0 small">Tổng vi phạm</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?= number_format($todayViolations ?? 0) ?></h3>
                        <p class="text-muted mb-0 small">Vi phạm hôm nay</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-success mb-2">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-0">3</h3>
                        <p class="text-muted mb-0 small">Loại phương tiện</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-info mb-2">
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?= number_format($totalViolations ? ceil($totalViolations / 100) : 0) ?>+</h3>
                        <p class="text-muted mb-0 small">Địa điểm</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content: News + Sidebar -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Left: Latest News -->
            <div class="col-lg-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="fw-bold mb-0"><i class="fas fa-newspaper me-2 text-primary"></i>Tin tức mới nhất</h3>
                    <a href="/tin-tuc" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
                </div>

                <div class="row g-4">
                    <?php if (!empty($latestNews)): ?>
                        <?php foreach ($latestNews as $news): ?>
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100 border-0">
                                    <?php if (!empty($news['thumbnail'])): ?>
                                        <img src="/<?= htmlspecialchars($news['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                                             class="card-img-top" alt="<?= htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8') ?>"
                                             style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                             style="height: 180px;">
                                            <i class="fas fa-newspaper fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <a href="/tin-tuc/<?= htmlspecialchars($news['slug'] ?? 'bai-viet-' . $news['id'], ENT_QUOTES, 'UTF-8') ?>"
                                               class="text-decoration-none text-dark stretched-link">
                                                <?= htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted small flex-grow-1">
                                            <?= htmlspecialchars(Helper::truncate(strip_tags($news['content'] ?? ''), 120), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <i class="far fa-calendar me-1"></i>
                                                <?= htmlspecialchars(Helper::formatDate($news['created_at'], 'd/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                            </small>
                                            <small class="text-muted">
                                                <i class="far fa-eye me-1"></i><?= (int)($news['views'] ?? 0) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-newspaper fa-3x mb-3"></i>
                                <p>Chưa có tin tức nào.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: Sidebar -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <!-- Top Offenses -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-dark fw-bold">
                        <i class="fas fa-list-ol me-2"></i>Top lỗi vi phạm
                    </div>
                    <div class="card-body p-0">
                        <?php if (!empty($topOffenses)): ?>
                            <ul class="list-group list-group-flush">
                                <?php $rank = 1; foreach ($topOffenses as $offense): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <span class="badge bg-secondary me-2">#<?= $rank ?></span>
                                            <?= htmlspecialchars($offense['offense_name'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <span class="badge bg-primary rounded-pill"><?= number_format($offense['count']) ?></span>
                                    </li>
                                <?php $rank++; endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <p class="mb-0 small">Chưa có dữ liệu.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick links -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        <i class="fas fa-bolt me-2"></i>Truy cập nhanh
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="/tra-cuu" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Tra cứu phạt nguội
                            </a>
                            <a href="/bien-bao" class="btn btn-outline-info">
                                <i class="fas fa-sign me-2"></i>Tra cứu biển báo
                            </a>
                            <a href="/thong-ke" class="btn btn-outline-success">
                                <i class="fas fa-chart-bar me-2"></i>Xem thống kê
                            </a>
                            <a href="/ban-do" class="btn btn-outline-warning">
                                <i class="fas fa-map me-2"></i>Bản đồ địa điểm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
