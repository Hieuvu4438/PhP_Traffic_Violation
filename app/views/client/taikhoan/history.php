<?php
use App\Core\Helper;

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
        <i class="fas fa-history me-2 text-primary"></i>Lịch sử tra cứu
    </h2>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <a href="/tai-khoan" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i>Quay lại tài khoản
    </a>

    <?php if (empty($history)): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-history fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Chưa có lịch sử tra cứu</h4>
                <p class="text-muted">Tất cả các lần tra cứu của bạn sẽ được lưu lại tại đây.</p>
                <a href="/tra-cuu" class="btn btn-primary">
                    <i class="fas fa-search me-2"></i>Tra cứu ngay
                </a>
            </div>
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 60px;">STT</th>
                            <th>Thời gian</th>
                            <th>Biển số xe</th>
                            <th>Loại xe</th>
                            <th class="text-center">Kết quả</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $stt = 1 + (($pagination['page'] ?? 1) - 1) * ($pagination['per_page'] ?? 15); foreach ($history as $item): ?>
                            <tr>
                                <td class="text-center"><?= $stt++ ?></td>
                                <td>
                                    <?= htmlspecialchars(Helper::formatDateTime($item['searched_at']), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($item['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </td>
                                <td><?= vehicleTypeBadge($item['vehicle_type']) ?></td>
                                <td class="text-center">
                                    <?php if ((int) $item['result_count'] > 0): ?>
                                        <span class="badge bg-danger"><?= $item['result_count'] ?> vi phạm</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Không vi phạm</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="/tra-cuu" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-search me-1"></i>Tra lại
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if (isset($pagination)): ?>
            <?php $baseUrl = '/tai-khoan/lich-su'; require __DIR__ . '/../../partials/pagination.php'; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
