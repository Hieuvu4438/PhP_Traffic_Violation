<?php
/**
 * Trang thống kê vi phạm (client/thongke/index.php)
 *
 * Chuẩn bị dữ liệu cho Chart.js:
 * - $offenseLabels / $offenseData: Top lỗi vi phạm -> bar chart ngang
 * - $locationLabels / $locationData: Top địa điểm -> bar chart ngang
 * - $months: 12 tháng tiếng Việt
 * - $years: 4 năm gần nhất để chọn (select box)
 * - $monthlyData: dữ liệu theo tháng cho line chart
 */
$offenseLabels = [];
$offenseData = [];
foreach ($topOffenses as $row) {
    $offenseLabels[] = $row['offense_name'];
    $offenseData[] = (int) $row['count'];
}

$locationLabels = [];
$locationData = [];
foreach ($topLocations as $row) {
    $locationLabels[] = $row['location_name'];
    $locationData[] = (int) $row['count'];
}

$months = ['January', 'February', 'March', 'April', 'May', 'June',
           'July', 'August', 'September', 'October', 'November', 'December'];

// range(date('Y'), date('Y') - 3): tạo mảng 4 năm từ năm hiện tại lùi về
$years = range(date('Y'), date('Y') - 3);

/**
 * Helper badge loại xe (dùng trong bảng Top biển số).
 */
function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Car</span>',
        'motorcycle' => '<span class="badge bg-info">Motorcycle</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Electric Motorcycle</span>',
        default => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    };
}
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-chart-bar: icon biểu đồ cột -->
        <i class="fas fa-chart-bar me-2 text-primary"></i>Traffic Violation Statistics
    </h2>

    <!-- === Selector chọn năm === -->
    <!-- d-flex justify-content-end: đẩy form chọn năm sang phải -->
    <div class="d-flex justify-content-end mb-3">
        <!-- form GET -> query string: /thong-ke?year=2025 -->
        <form method="GET" action="/thong-ke" class="d-flex align-items-center gap-2">
            <!-- form-label fw-bold mb-0: nhãn in đậm, không margin bottom -->
            <label for="year" class="form-label mb-0 fw-bold">Year:</label>
            <!-- form-select-sm: select kích thước nhỏ | onchange="this.form.submit()" -> tự submit khi chọn -->
            <select class="form-select form-select-sm" id="year" name="year" onchange="this.form.submit()"
                    style="width: auto;">
                <?php foreach ($years as $y): ?>
                    <!-- Đánh dấu selected cho năm hiện tại hoặc năm đã chọn -->
                    <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- === Thẻ thống kê trạng thái (4 cột) === -->
    <!-- row g-3: grid gap 1rem -->
    <div class="row g-3 mb-4">
        <!-- Chưa xử lý: nền đỏ nhạt (bg-danger bg-opacity-10) -->
        <div class="col-md-3 col-6">
            <!-- card border-0 shadow-sm text-center h-100: không viền, đổ bóng, căn giữa, full cao -->
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <!-- text-danger: chữ đỏ -->
                    <h3 class="fw-bold text-danger mb-0"><?= number_format($statusCounts['pending'] ?? 0) ?></h3>
                    <p class="text-muted mb-0 small">Pending</p>
                </div>
            </div>
        </div>
        <!-- Đã xử lý: nền vàng nhạt -->
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <h3 class="fw-bold text-warning mb-0"><?= number_format($statusCounts['processed'] ?? 0) ?></h3>
                    <p class="text-muted mb-0 small">Processed</p>
                </div>
            </div>
        </div>
        <!-- Đã nộp phạt: nền xanh lá nhạt -->
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <h3 class="fw-bold text-success mb-0"><?= number_format($statusCounts['paid'] ?? 0) ?></h3>
                    <p class="text-muted mb-0 small">Paid</p>
                </div>
            </div>
        </div>
        <!-- Tổng cộng: nền xanh nhạt | array_sum: tổng 3 trạng thái -->
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <h3 class="fw-bold text-info mb-0">
                        <?= number_format(array_sum($statusCounts)) ?>
                    </h3>
                    <p class="text-muted mb-0 small">Total</p>
                </div>
            </div>
        </div>
    </div>

    <!-- === Biểu đồ === -->
    <!-- row g-4: grid gap 1.5rem -->
    <div class="row g-4 mb-4">
        <!-- Top lỗi vi phạm: biểu đồ cột ngang (col-lg-6: 6/12 desktop, full mobile) -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-list-ol: icon danh sách xếp hạng -->
                    <i class="fas fa-list-ol me-2"></i>Top Violations
                </div>
                <div class="card-body">
                    <?php if (empty($topOffenses)): ?>
                        <div class="text-center py-4 text-muted">No data available.</div>
                    <?php else: ?>
                        <!-- Canvas cho Chart.js, id="offenseChart" -->
                        <canvas id="offenseChart" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top địa điểm: biểu đồ cột ngang -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-info text-white fw-bold">
                    <!-- fa-map-marker-alt: icon địa điểm bản đồ -->
                    <i class="fas fa-map-marker-alt me-2"></i>Top Violation Locations
                </div>
                <div class="card-body">
                    <?php if (empty($topLocations)): ?>
                        <div class="text-center py-4 text-muted">No data available.</div>
                    <?php else: ?>
                        <canvas id="locationChart" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- === Biểu đồ đường theo tháng === -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <!-- card-header bg-warning text-dark: header vàng chữ tối -->
                <div class="card-header bg-warning text-dark fw-bold">
                    <!-- fa-chart-line: icon biểu đồ đường -->
                    <i class="fas fa-chart-line me-2"></i>Monthly Violations - Year <?= $year ?>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- === Bảng Top biển số vi phạm nhiều nhất === -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <!-- card-header bg-success text-white: header xanh lá chữ trắng -->
                <div class="card-header bg-success text-white fw-bold">
                    <!-- fa-car: icon xe ô tô -->
                    <i class="fas fa-car me-2"></i>Top License Plates with Most Violations
                </div>
                <!-- table-responsive: bảng cuộn ngang trên mobile -->
                <div class="table-responsive">
                    <!-- table-hover: bảng đổi màu dòng khi hover -->
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <!-- text-center style width: căn giữa + cố định chiều rộng cột STT -->
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>License Plate</th>
                                <th>Vehicle Type</th>
                                <th class="text-center">Violations</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topPlates)): ?>
                                <!-- colspan="4": gộp 4 cột cho dòng thông báo rỗng -->
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No data available.</td>
                                </tr>
                            <?php else: ?>
                                <!-- $rank: thứ hạng 1, 2, 3... -->
                                <?php $rank = 1; foreach ($topPlates as $plate): ?>
                                    <tr>
                                        <td class="text-center">
                                            <!-- Top 3: hiển thị badge màu đặc biệt
                                                 #1: vàng (bg-warning), #2: xám (bg-secondary), #3: xanh nhạt (bg-info)
                                                 Từ #4 trở đi: chỉ hiển thị số -->
                                            <?php if ($rank <= 3): ?>
                                                <span class="badge bg-<?= $rank === 1 ? 'warning' : ($rank === 2 ? 'secondary' : 'info') ?>">
                                                    #<?= $rank ?>
                                                </span>
                                            <?php else: ?>
                                                <?= $rank ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($plate['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        </td>
                                        <td><?= vehicleTypeBadge($plate['vehicle_type']) ?></td>
                                        <td class="text-center">
                                            <!-- badge bg-danger: huy hiệu đỏ, số lần vi phạm -->
                                            <span class="badge bg-danger"><?= number_format($plate['count']) ?></span>
                                        </td>
                                    </tr>
                                <?php $rank++; endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- === Chart.js CDN === -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>

<!-- === Khởi tạo biểu đồ Chart.js === -->
<script>
// DOMContentLoaded: đảm bảo DOM sẵn sàng trước khi vẽ chart
document.addEventListener('DOMContentLoaded', function() {
    // Bảng màu cho các cột biểu đồ (10 màu, xoay vòng)
    var colors = ['#0d6efd','#6610f2','#6f42c1','#d63384','#dc3545','#fd7e14','#ffc107','#198754','#20c997','#0dcaf0'];

    // === Biểu đồ Top Lỗi Vi Phạm (Bar ngang - indexAxis: 'y') ===
    var offenseCtx = document.getElementById('offenseChart');
    if (offenseCtx) {
        new Chart(offenseCtx, {
            type: 'bar',                     // Biểu đồ cột
            data: {
                // json_encode: chuyển mảng PHP labels sang JS array
                labels: <?= json_encode($offenseLabels, JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: 'Number of violations', // Chú thích dataset
                    data: <?= json_encode($offenseData) ?>,
                    backgroundColor: colors, // Màu nền cột
                    borderColor: colors,     // Màu viền cột
                    borderWidth: 1           // Độ dày viền
                }]
            },
            options: {
                indexAxis: 'y',              // Đổi trục: thanh nằm ngang (dễ đọc tên lỗi dài)
                responsive: true,            // Tự co giãn theo container
                plugins: {
                    legend: { display: false } // Ẩn chú thích (không cần vì chỉ 1 dataset)
                },
                scales: {
                    x: {
                        beginAtZero: true,     // Trục X bắt đầu từ 0
                        ticks: { precision: 0 } // Giá trị trục là số nguyên
                    }
                }
            }
        });
    }

    // === Biểu đồ Top Địa Điểm (Bar ngang - cấu trúc tương tự offenseChart) ===
    var locationCtx = document.getElementById('locationChart');
    if (locationCtx) {
        new Chart(locationCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($locationLabels, JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: 'Số lần vi phạm',
                    data: <?= json_encode($locationData) ?>,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',              // Cột ngang
                responsive: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }

    // === Biểu đồ Đường Theo Tháng (Line chart) ===
    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',                   // Biểu đồ đường
            data: {
                labels: <?= json_encode($months, JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: 'Violations in <?= $year ?>',
                    data: <?= json_encode($monthlyData) ?>,
                    borderColor: '#dc3545',  // Đường màu đỏ
                    backgroundColor: 'rgba(220, 53, 69, 0.1)', // Nền đỏ trong suốt dưới đường
                    fill: true,              // Tô màu nền dưới đường
                    tension: 0.3,            // Độ cong của đường (0 = thẳng)
                    pointBackgroundColor: '#dc3545', // Màu chấm tròn tại điểm dữ liệu
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' } // Hiển thị chú thích phía trên
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>
