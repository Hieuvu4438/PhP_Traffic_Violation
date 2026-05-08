<?php
use App\Core\Session;
use App\Core\Helper;

/**
 * Helper: trả về badge HTML dựa theo loại phương tiện.
 * Sử dụng match expression của PHP 8.x để map type -> badge Bootstrap.
 * badge bg-primary: nền xanh cho Ô tô
 * badge bg-info: nền xanh nhạt cho Xe máy
 * badge bg-success: nền xanh lá cho Xe máy điện
 */
function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => $type,
    };
}
?>

<!--
  Trang chủ (client/home/index.php)
  Các section chính:
  1. Hero Section: form tra cứu nhanh nổi bật với nền xanh
  2. Stat Cards: 4 thẻ thống kê (tổng vi phạm, hôm nay, loại xe, địa điểm)
  3. Main Content: 2 cột - tin tức mới nhất (trái) + sidebar (phải)
     - Sidebar: Top lỗi vi phạm + liên kết truy cập nhanh
-->

<!-- Hero Section: nền xanh (bg-primary), chữ trắng (text-white), padding top/bottom 3rem -->
<section class="hero-section bg-primary text-white py-5">
    <!-- container: căn giữa nội dung theo breakpoint -->
    <div class="container">
        <!-- align-items-center: căn giữa theo chiều dọc -->
        <div class="row align-items-center">
            <!-- col-lg-8: chiếm 8/12 trên desktop, full trên mobile -->
            <div class="col-lg-8">
                <!-- fw-bold: in đậm | mb-3: margin-bottom 1rem -->
                <h1 class="fw-bold mb-3">Tra Cứu Phương Tiện Vi Phạm Giao Thông</h1>
                <!-- lead: chữ to hơn bình thường | mb-4: margin-bottom 1.5rem -->
                <p class="lead mb-4">Kiểm tra phạt nguội toàn quốc nhanh chóng, chính xác. Dữ liệu từ Cục CSGT & Cục Đăng Kiểm Việt Nam.</p>

                <!-- Form tra cứu nhanh trong Hero -->
                <!-- bg-white bg-opacity-10: nền trắng trong suốt 10% | rounded-3: bo góc lớn -->
                <div class="hero-search p-4 bg-white bg-opacity-10 rounded-3 backdrop-blur">
                    <!-- Form POST đến /tra-cuu -->
                    <!-- row g-3: hàng grid với gutter gap 1rem giữa các cột -->
                    <form method="POST" action="/tra-cuu" class="row g-3">
                        <!-- CSRF token bảo vệ form -->
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">

                        <!-- col-md-5: 5/12 trên desktop, full trên mobile -->
                        <div class="col-md-5">
                            <!-- form-label text-white: nhãn form màu trắng trên nền xanh -->
                            <label for="plate_number" class="form-label text-white">Biển số xe</label>
                            <!-- form-control-lg: input kích thước lớn -->
                            <input type="text" class="form-control form-control-lg" id="plate_number"
                                   name="plate_number" placeholder="VD: 30A-12345" required>
                        </div>

                        <!-- col-md-4: 4/12 trên desktop -->
                        <div class="col-md-4">
                            <label for="vehicle_type" class="form-label text-white">Loại xe</label>
                            <!-- form-select-lg: select kích thước lớn -->
                            <select class="form-select form-select-lg" id="vehicle_type" name="vehicle_type" required>
                                <option value="">-- Chọn loại xe --</option>
                                <option value="car">Ô tô</option>
                                <option value="motorcycle">Xe máy</option>
                                <option value="electric_motorcycle">Xe máy điện</option>
                            </select>
                        </div>

                        <!-- col-md-3: 3/12 trên desktop | d-flex align-items-end: đẩy nút xuống cuối hàng -->
                        <div class="col-md-3 d-flex align-items-end">
                            <!-- btn-warning: nút màu cam | btn-lg: lớn | w-100: full width | fw-bold: in đậm -->
                            <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">
                                <!-- fa-search: icon kính lúp (tìm kiếm) -->
                                <i class="fas fa-search me-2"></i>Tra cứu ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- col-lg-4: 4/12 trên desktop | d-none d-lg-block: ẩn trên mobile, hiện từ desktop -->
            <div class="col-lg-4 d-none d-lg-block text-center">
                <!-- fa-shield-halved: icon khiên chắn (an toàn, bảo vệ) | opacity 0.3 -> mờ làm nền -->
                <i class="fas fa-shield-halved" style="font-size: 12rem; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Stat Cards: 4 thẻ thống kê ngay dưới hero -->
<!-- border-bottom: đường viền dưới phân cách section -->
<section class="py-4 border-bottom">
    <div class="container">
        <!-- row g-4: hàng grid với gutter gap 1.5rem -->
        <div class="row g-4">
            <!-- col-md-3 col-6: 3/12 trên desktop, 6/12 (2 cột) trên mobile -->
            <!-- Tổng vi phạm -->
            <div class="col-md-3 col-6">
                <!-- card border-0: xóa viền | shadow-sm: đổ bóng nhẹ | h-100: full chiều cao -->
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-primary mb-2">
                            <!-- fa-exclamation-triangle: icon tam giác cảnh báo -->
                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                        </div>
                        <!-- number_format: định dạng số với dấu phẩy ngăn cách hàng nghìn -->
                        <h3 class="fw-bold mb-0"><?= number_format($totalViolations ?? 0) ?></h3>
                        <p class="text-muted mb-0 small">Tổng vi phạm</p>
                    </div>
                </div>
            </div>

            <!-- Vi phạm hôm nay -->
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-warning mb-2">
                            <!-- fa-clock: icon đồng hồ (mới, gần đây) -->
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-0"><?= number_format($todayViolations ?? 0) ?></h3>
                        <p class="text-muted mb-0 small">Vi phạm hôm nay</p>
                    </div>
                </div>
            </div>

            <!-- Loại phương tiện -->
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-success mb-2">
                            <!-- fa-check-circle: icon dấu check trong vòng tròn -->
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                        <h3 class="fw-bold mb-0">3</h3>
                        <p class="text-muted mb-0 small">Loại phương tiện</p>
                    </div>
                </div>
            </div>

            <!-- Địa điểm -->
            <div class="col-md-3 col-6">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-info mb-2">
                            <!-- fa-map-marker-alt: icon địa điểm trên bản đồ -->
                            <i class="fas fa-map-marker-alt fa-2x"></i>
                        </div>
                        <!-- ceil($totalViolations / 100): ước tính số địa điểm, hiển thị dạng ước lượng (dấu +) -->
                        <h3 class="fw-bold mb-0"><?= number_format($totalViolations ? ceil($totalViolations / 100) : 0) ?>+</h3>
                        <p class="text-muted mb-0 small">Địa điểm</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content: Tin tức + Sidebar -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Cột trái: Tin tức mới nhất (8/12 trên desktop) -->
            <div class="col-lg-8">
                <!-- d-flex justify-content-between: tiêu đề trái + nút "Xem tất cả" phải -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <!-- fa-newspaper: icon tờ báo (tin tức) -->
                    <h3 class="fw-bold mb-0"><i class="fas fa-newspaper me-2 text-primary"></i>Tin tức mới nhất</h3>
                    <!-- btn-outline-primary btn-sm: nút viền xanh, kích thước nhỏ -->
                    <a href="/tin-tuc" class="btn btn-outline-primary btn-sm">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
                </div>

                <!-- Lưới tin tức 2 cột trên desktop (col-md-6) -->
                <div class="row g-4">
                    <!-- Kiểm tra có tin tức không -->
                    <?php if (!empty($latestNews)): ?>
                        <!-- foreach lặp qua từng bài viết -->
                        <?php foreach ($latestNews as $news): ?>
                            <div class="col-md-6">
                                <!-- h-100: full chiều cao hàng | border-0: không viền -->
                                <div class="card shadow-sm h-100 border-0">
                                    <!-- Nếu có thumbnail thì hiển thị ảnh -->
                                    <?php if (!empty($news['thumbnail'])): ?>
                                        <!-- htmlspecialchars: encode HTML entities chống XSS -->
                                        <!-- object-fit: cover: ảnh cắt vừa khung, giữ tỷ lệ -->
                                        <img src="/<?= htmlspecialchars($news['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                                             class="card-img-top" alt="<?= htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8') ?>"
                                             style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <!-- Không có thumbnail: hiển thị icon mặc định -->
                                        <!-- bg-light: nền xám nhạt | d-flex: flexbox để căn giữa icon -->
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                             style="height: 180px;">
                                            <i class="fas fa-newspaper fa-3x text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                    <!-- d-flex flex-column: flex column để đẩy footer xuống dưới -->
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <!-- stretched-link: làm toàn bộ card thành link -->
                                            <!-- Slug friendly URL: /tin-tuc/slug-hoac-id -->
                                            <a href="/tin-tuc/<?= htmlspecialchars($news['slug'] ?? 'bai-viet-' . $news['id'], ENT_QUOTES, 'UTF-8') ?>"
                                               class="text-decoration-none text-dark stretched-link">
                                                <?= htmlspecialchars($news['title'], ENT_QUOTES, 'UTF-8') ?>
                                            </a>
                                        </h5>
                                        <!-- Helper::truncate: cắt nội dung 120 ký tự | strip_tags: loại bỏ HTML -->
                                        <!-- flex-grow-1: chiếm hết không gian còn lại, đẩy footer xuống -->
                                        <p class="card-text text-muted small flex-grow-1">
                                            <?= htmlspecialchars(Helper::truncate(strip_tags($news['content'] ?? ''), 120), ENT_QUOTES, 'UTF-8') ?>
                                        </p>
                                        <!-- Footer card: ngày đăng + lượt xem -->
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <small class="text-muted">
                                                <!-- far fa-calendar: icon lịch (regular style) -->
                                                <i class="far fa-calendar me-1"></i>
                                                <!-- Helper::formatDate: định dạng ngày tháng dd/mm/YYYY -->
                                                <?= htmlspecialchars(Helper::formatDate($news['created_at'], 'd/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                            </small>
                                            <small class="text-muted">
                                                <!-- far fa-eye: icon con mắt (lượt xem - regular style) -->
                                                <i class="far fa-eye me-1"></i><?= (int)($news['views'] ?? 0) ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Trạng thái rỗng: chưa có tin tức -->
                        <div class="col-12">
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-newspaper fa-3x mb-3"></i>
                                <p>Chưa có tin tức nào.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Cột phải: Sidebar (4/12 trên desktop) -->
            <!-- mt-4 mt-lg-0: margin-top 1.5rem trên mobile, 0 trên desktop -->
            <div class="col-lg-4 mt-4 mt-lg-0">
                <!-- Sidebar block 1: Top lỗi vi phạm -->
                <!-- card-header bg-warning: header nền vàng |
                     d-flex justify-content-between: tiêu đề trái + số phải -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-warning text-dark fw-bold">
                        <!-- fa-list-ol: icon danh sách số (xếp hạng) -->
                        <i class="fas fa-list-ol me-2"></i>Top lỗi vi phạm
                    </div>
                    <!-- p-0: không padding để list-group sát viền -->
                    <div class="card-body p-0">
                        <?php if (!empty($topOffenses)): ?>
                            <!-- list-group-flush: bỏ bo góc ngoài của list group -->
                            <ul class="list-group list-group-flush">
                                <!-- $rank: biến đếm thứ hạng -->
                                <?php $rank = 1; foreach ($topOffenses as $offense): ?>
                                    <!-- d-flex justify-content-between: tên lỗi trái + số lần phải -->
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <span>
                                            <!-- badge bg-secondary: huy hiệu nền xám, hiển thị thứ hạng #1, #2... -->
                                            <span class="badge bg-secondary me-2">#<?= $rank ?></span>
                                            <?= htmlspecialchars($offense['offense_name'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                        <!-- badge bg-primary rounded-pill: huy hiệu xanh dạng pill, hiển thị số lần -->
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

                <!-- Sidebar block 2: Liên kết truy cập nhanh -->
                <!-- card-header bg-primary text-white: header nền xanh chữ trắng -->
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white fw-bold">
                        <!-- fa-bolt: icon tia sét (nhanh chóng) -->
                        <i class="fas fa-bolt me-2"></i>Truy cập nhanh
                    </div>
                    <div class="card-body">
                        <!-- d-grid gap-2: các nút xếp dạng grid, cách nhau 0.5rem -->
                        <div class="d-grid gap-2">
                            <a href="/tra-cuu" class="btn btn-outline-primary">
                                <i class="fas fa-search me-2"></i>Tra cứu phạt nguội
                            </a>
                            <a href="/bien-bao" class="btn btn-outline-info">
                                <!-- fa-sign: icon biển báo -->
                                <i class="fas fa-sign me-2"></i>Tra cứu biển báo
                            </a>
                            <a href="/thong-ke" class="btn btn-outline-success">
                                <!-- fa-chart-bar: icon biểu đồ cột -->
                                <i class="fas fa-chart-bar me-2"></i>Xem thống kê
                            </a>
                            <a href="/ban-do" class="btn btn-outline-warning">
                                <!-- fa-map: icon bản đồ -->
                                <i class="fas fa-map me-2"></i>Bản đồ địa điểm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
