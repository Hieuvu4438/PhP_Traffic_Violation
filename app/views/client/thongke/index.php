<?php
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

$months = ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6',
           'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12'];

$years = range(date('Y'), date('Y') - 3);

function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    };
}
?>

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <i class="fas fa-chart-bar me-2 text-primary"></i>Thống kê vi phạm giao thông
    </h2>

    <!-- Year selector -->
    <div class="d-flex justify-content-end mb-3">
        <form method="GET" action="/thong-ke" class="d-flex align-items-center gap-2">
            <label for="year" class="form-label mb-0 fw-bold">Năm:</label>
            <select class="form-select form-select-sm" id="year" name="year" onchange="this.form.submit()"
                    style="width: auto;">
                <?php foreach ($years as $y): ?>
                    <option value="<?= $y ?>" <?= ($year ?? date('Y')) == $y ? 'selected' : '' ?>><?= $y ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- Status Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-danger bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <h3 class="fw-bold text-danger mb-0"><?= number_format($statusCounts['pending'] ?? 0) ?></h3>
                    <p class="text-muted mb-0 small">Chưa xử lý</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-warning bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <h3 class="fw-bold text-warning mb-0"><?= number_format($statusCounts['processed'] ?? 0) ?></h3>
                    <p class="text-muted mb-0 small">Đã xử lý</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-success bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <h3 class="fw-bold text-success mb-0"><?= number_format($statusCounts['paid'] ?? 0) ?></h3>
                    <p class="text-muted mb-0 small">Đã nộp phạt</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card border-0 shadow-sm bg-info bg-opacity-10 text-center h-100">
                <div class="card-body">
                    <h3 class="fw-bold text-info mb-0">
                        <?= number_format(array_sum($statusCounts)) ?>
                    </h3>
                    <p class="text-muted mb-0 small">Tổng cộng</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row g-4 mb-4">
        <!-- Top Offenses -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fas fa-list-ol me-2"></i>Top lỗi vi phạm
                </div>
                <div class="card-body">
                    <?php if (empty($topOffenses)): ?>
                        <div class="text-center py-4 text-muted">Chưa có dữ liệu.</div>
                    <?php else: ?>
                        <canvas id="offenseChart" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Top Locations -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-info text-white fw-bold">
                    <i class="fas fa-map-marker-alt me-2"></i>Top địa điểm vi phạm
                </div>
                <div class="card-body">
                    <?php if (empty($topLocations)): ?>
                        <div class="text-center py-4 text-muted">Chưa có dữ liệu.</div>
                    <?php else: ?>
                        <canvas id="locationChart" height="300"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Line Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-chart-line me-2"></i>Vi phạm theo tháng - Năm <?= $year ?>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Plates Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white fw-bold">
                    <i class="fas fa-car me-2"></i>Top biển số vi phạm nhiều nhất
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center" style="width: 60px;">#</th>
                                <th>Biển số xe</th>
                                <th>Loại xe</th>
                                <th class="text-center">Số lần vi phạm</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($topPlates)): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Chưa có dữ liệu.</td>
                                </tr>
                            <?php else: ?>
                                <?php $rank = 1; foreach ($topPlates as $plate): ?>
                                    <tr>
                                        <td class="text-center">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var colors = ['#0d6efd','#6610f2','#6f42c1','#d63384','#dc3545','#fd7e14','#ffc107','#198754','#20c997','#0dcaf0'];

    // Top Offenses Bar Chart
    var offenseCtx = document.getElementById('offenseChart');
    if (offenseCtx) {
        new Chart(offenseCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($offenseLabels, JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: 'Số lần vi phạm',
                    data: <?= json_encode($offenseData) ?>,
                    backgroundColor: colors,
                    borderColor: colors,
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
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

    // Top Locations Bar Chart
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
                indexAxis: 'y',
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

    // Monthly Line Chart
    var monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode($months, JSON_UNESCAPED_UNICODE) ?>,
                datasets: [{
                    label: 'Số vi phạm năm <?= $year ?>',
                    data: <?= json_encode($monthlyData) ?>,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.1)',
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#dc3545',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'top' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    }
});
</script>
