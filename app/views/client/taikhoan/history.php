<?php
use App\Core\Helper;

/**
 * Helper: trả về badge Bootstrap theo loại phương tiện.
 * Dùng trong bảng lịch sử tra cứu.
 */
function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    };
}
?>

<!--
  Trang lịch sử tra cứu (client/taikhoan/history.php)
  - Bảng dữ liệu toàn bộ lịch sử tra cứu của user
  - Có phân trang (pagination)
  - Mỗi dòng: STT (có tính trang), thời gian, biển số, loại xe (badge), kết quả (badge), hành động (tra lại)
  - Nút "Tra lại" link đến /tra-cuu
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-history: icon đồng hồ lịch sử -->
        <i class="fas fa-history me-2 text-primary"></i>Lịch sử tra cứu
    </h2>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- Nút quay lại dashboard -->
    <a href="/tai-khoan" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i>Quay lại tài khoản
    </a>

    <?php if (empty($history)): ?>
        <!-- Trạng thái rỗng -->
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
            <!-- table-responsive: cuộn ngang trên mobile -->
            <div class="table-responsive">
                <!-- table-hover: bảng đổi màu dòng khi hover -->
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <!-- style="width: 60px": cố định chiều rộng cột STT -->
                            <th class="text-center" style="width: 60px;">STT</th>
                            <th>Thời gian</th>
                            <th>Biển số xe</th>
                            <th>Loại xe</th>
                            <th class="text-center">Kết quả</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!--
                          Tính STT có tính phân trang:
                          ($pagination['page'] - 1) * $pagination['per_page'] + 1
                          VD: trang 2, 15 dòng/trang -> STT bắt đầu từ 16
                        -->
                        <?php $stt = 1 + (($pagination['page'] ?? 1) - 1) * ($pagination['per_page'] ?? 15); foreach ($history as $item): ?>
                            <tr>
                                <td class="text-center"><?= $stt++ ?></td>
                                <td>
                                    <!-- formatDateTime: ngày giờ đầy đủ -->
                                    <?= htmlspecialchars(Helper::formatDateTime($item['searched_at']), ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($item['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong>
                                </td>
                                <td><?= vehicleTypeBadge($item['vehicle_type']) ?></td>
                                <td class="text-center">
                                    <!-- Có vi phạm: badge đỏ | Không: badge xanh lá -->
                                    <?php if ((int) $item['result_count'] > 0): ?>
                                        <span class="badge bg-danger"><?= $item['result_count'] ?> vi phạm</span>
                                    <?php else: ?>
                                        <span class="badge bg-success">Không vi phạm</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <!-- Nút "Tra lại": link đến trang tra cứu -->
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

        <!-- Phân trang (nếu có $pagination) -->
        <?php if (isset($pagination)): ?>
            <!-- $baseUrl: URL gốc cho phân trang -->
            <?php $baseUrl = '/tai-khoan/lich-su'; require __DIR__ . '/../../partials/pagination.php'; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
