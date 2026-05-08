<?php
// ==================================================================================
// VIEW: Trang Dashboard (Bảng điều khiển) dành cho Admin
// Hiển thị 4 thẻ thống kê tổng quan (Users, Vi phạm, Tin tức, Phương tiện)
// và bảng liệt kê các vi phạm gần đây nhất.
// Dữ liệu được controller truyền vào: $totalUsers, $totalViolations, $totalNews,
// $totalVehicles, $recentViolations (mảng các vi phạm mới nhất).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash (thành công / lỗi) từ session
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Tiêu đề trang ========== -->
<!-- class="mb-4" = margin-bottom 1.5rem -->
<h2 class="mb-4">Dashboard</h2>

<!-- ========== Hàng thẻ thống kê (4 thẻ, mỗi thẻ rộng 3 cột trên desktop) ========== -->
<!-- row: hàng flex, g-3: khoảng cách giữa các cột 1rem, mb-4: margin-bottom 1.5rem -->
<div class="row g-3 mb-4">
    <!-- ===== Thẻ 1: Tổng người dùng ===== -->
    <!-- col-md-3: chiếm 3/12 cột trên màn hình >=768px -->
    <div class="col-md-3">
        <!-- card: thẻ Bootstrap, text-bg-primary: nền xanh dương + chữ trắng -->
        <div class="card text-bg-primary">
            <div class="card-body">
                <!-- d-flex: flexbox, justify-content-between: căn đều 2 bên, align-items-center: căn dọc giữa -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <!-- card-title: tiêu đề thẻ, mb-0: không margin-bottom -->
                        <h5 class="card-title mb-0"><?= $totalUsers ?></h5>
                        <small>Nguời dùng</small>
                    </div>
                    <!-- fas fa-users: icon Font Awesome "nhóm người", fa-2x: kích thước gấp 2, opacity-50: mờ 50% -->
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Thẻ 2: Tổng vi phạm ===== -->
    <!-- text-bg-danger: nền đỏ + chữ trắng -->
    <div class="col-md-3">
        <div class="card text-bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?= $totalViolations ?></h5>
                        <small>Vi phạm</small>
                    </div>
                    <!-- fa-exclamation-triangle: icon tam giác cảnh báo -->
                    <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Thẻ 3: Tổng tin tức ===== -->
    <!-- text-bg-success: nền xanh lá + chữ trắng -->
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?= $totalNews ?></h5>
                        <small>Tin tức</small>
                    </div>
                    <!-- fa-newspaper: icon tờ báo -->
                    <i class="fas fa-newspaper fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Thẻ 4: Tổng phương tiện ===== -->
    <!-- text-bg-warning: nền vàng + chữ tối -->
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?= $totalVehicles ?></h5>
                        <small>Phuơng tiện</small>
                    </div>
                    <!-- fa-car: icon xe ô tô -->
                    <i class="fas fa-car fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== Bảng vi phạm gần đây ========== -->
<!-- card: thẻ chứa toàn bộ bảng -->
<div class="card">
    <!-- card-header: phần đầu thẻ (tiêu đề + nút), d-flex...align-items-center: flexbox căn đều 2 bên, căn dọc giữa -->
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Vi phạm gần đây</h5>
        <!-- btn btn-sm btn-primary: nút nhỏ (small) màu xanh dương, link đến trang quản lý vi phạm -->
        <a href="/admin/violations" class="btn btn-sm btn-primary">Xem tất cả</a>
    </div>
    <!-- card-body p-0: nội dung thẻ, padding = 0 (để bảng dính sát viền thẻ) -->
    <div class="card-body p-0">
        <!-- table: bảng Bootstrap, table-hover: highlight dòng khi rê chuột, mb-0: không margin-bottom -->
        <table class="table table-hover mb-0">
            <!-- thead table-light: đầu bảng nền xám nhạt -->
            <thead class="table-light">
                <tr>
                    <th>Biển số</th>
                    <th>Lỗi vi phạm</th>
                    <th>Địa điểm</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Kiểm tra mảng rỗng — nếu không có vi phạm gần đây nào thì hiển thị dòng trống
                if (empty($recentViolations)): ?>
                    <!-- text-center: căn giữa, text-muted: chữ xám mờ, py-3: padding trên/dưới 1rem, colspan 5 vì bảng có 5 cột -->
                    <tr><td colspan="5" class="text-center text-muted py-3">Chưa có vi phạm nào.</td></tr>
                <?php else:
                    // Lặp qua từng vi phạm trong mảng $recentViolations
                    foreach ($recentViolations as $v): ?>
                <tr>
                    <!-- Biển số: in đậm, đã escape XSS -->
                    <td><strong><?= htmlspecialchars($v['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <!-- Tên lỗi vi phạm — nếu null thì hiển thị dấu gạch ngang -->
                    <td><?= htmlspecialchars($v['offense_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Tên địa điểm — nếu null thì hiển thị dấu gạch ngang -->
                    <td><?= htmlspecialchars($v['location_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Định dạng ngày giờ bằng Helper::formatDateTime() -->
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDateTime($v['violation_date']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                        // Dùng match() để hiển thị badge màu tương ứng với trạng thái:
                        // - pending (chưa xử lý) => badge đỏ (bg-danger)
                        // - processed (đã xử lý) => badge vàng chữ đen (bg-warning text-dark)
                        // - paid (đã nộp phạt) => badge xanh lá (bg-success)
                        // - default => badge xám (bg-secondary), hiển thị nguyên trạng thái thô
                        echo match($v['status']) {
                            'pending' => '<span class="badge bg-danger">Chưa xử lý</span>',
                            'processed' => '<span class="badge bg-warning text-dark">Đã xử lý</span>',
                            'paid' => '<span class="badge bg-success">Đã nộp phạt</span>',
                            default => '<span class="badge bg-secondary">' . htmlspecialchars($v['status'], ENT_QUOTES, 'UTF-8') . '</span>',
                        };
                        ?>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
