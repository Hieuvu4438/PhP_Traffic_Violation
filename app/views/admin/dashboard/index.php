<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<h2 class="mb-4">Dashboard</h2>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card text-bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?= $totalUsers ?></h5>
                        <small>Người dùng</small>
                    </div>
                    <i class="fas fa-users fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-danger">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?= $totalViolations ?></h5>
                        <small>Vi phạm</small>
                    </div>
                    <i class="fas fa-exclamation-triangle fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?= $totalNews ?></h5>
                        <small>Tin tức</small>
                    </div>
                    <i class="fas fa-newspaper fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title mb-0"><?= $totalVehicles ?></h5>
                        <small>Phương tiện</small>
                    </div>
                    <i class="fas fa-car fa-2x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Vi phạm gần đây</h5>
        <a href="/admin/violations" class="btn btn-sm btn-primary">Xem tất cả</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
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
                <?php if (empty($recentViolations)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Chưa có vi phạm nào.</td></tr>
                <?php else: foreach ($recentViolations as $v): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($v['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= htmlspecialchars($v['offense_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['location_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDateTime($v['violation_date']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
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
