<?php
// ==================================================================================
// VIEW: Quản lý cảnh báo giao thông (Admin) - Danh sách
// Hiển thị bảng danh sách cảnh báo: ID, tiêu đề, loại (Tai nạn/Ùn tắc/Công trình/
// Thời tiết/Khác), ngày hết hạn, trạng thái (Đang hiển thị/Ẩn), ngày tạo,
// kèm nút Sửa/Xóa và phân trang.
// Dữ liệu: $items (danh sách cảnh báo), $data (thông tin phân trang).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Hàng tiêu đề + nút Thêm cảnh báo ========== -->
<!-- d-flex...align-items-center: flexbox căn đều 2 bên trái-phải, căn dọc giữa; mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Manage Traffic Alerts</h2>
    <!-- btn btn-primary: nút xanh dương; fa-plus me-1: icon dấu cộng + margin-right 0.25rem -->
    <a href="/admin/alerts/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add Alert</a>
</div>

<!-- ========== Bảng danh sách cảnh báo ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ không padding -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng hover dòng, không margin-bottom -->
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Expires</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <!-- colspan 7: gộp 7 cột; text-center text-muted py-3: căn giữa, chữ xám, padding trên/dưới 1rem -->
                    <tr><td colspan="7" class="text-center text-muted py-3">No alerts found.</td></tr>
                <?php else:
                    foreach ($items as $alert): ?>
                <tr>
                    <td><?= $alert['id'] ?></td>
                    <!-- Cắt tiêu đề còn 60 ký tự nếu dài -->
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($alert['title'], 60), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                        // Map giá trị alert_type (tiếng Anh trong DB) sang nhãn tiếng Việt:
                        // accident => Tai nạn, congestion => Ùn tắc, construction => Công trình,
                        // weather => Thời tiết, other => Khác
                        $alertTypeLabels = [
                            'accident' => 'Accident',
                            'congestion' => 'Congestion',
                            'construction' => 'Construction',
                            'weather' => 'Weather',
                            'other' => 'Other',
                        ];
                        $type = $alert['alert_type'] ?? 'other';
                        echo htmlspecialchars($alertTypeLabels[$type] ?? $type, ENT_QUOTES, 'UTF-8');
                        ?>
                    </td>
                    <!-- Ngày hết hạn: nếu null thì hiển thị dấu gạch ngang -->
                    <td><?= !empty($alert['expires_at']) ? htmlspecialchars(\App\Core\Helper::formatDate($alert['expires_at']), ENT_QUOTES, 'UTF-8') : '—' ?></td>
                    <td>
                        <!-- Badge trạng thái:
                             status=1 => bg-success (xanh lá): Đang hiển thị
                             status=0 => bg-secondary (xám): Ẩn -->
                        <span class="badge <?= $alert['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $alert['status'] ? 'Active' : 'Hidden' ?>
                        </span>
                    </td>
                    <!-- formatDate(): định dạng ngày tạo -->
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($alert['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <!-- Nút Sửa: btn-sm = nút nhỏ, btn-warning = vàng, fa-edit = icon bút sửa -->
                        <a href="/admin/alerts/<?= $alert['id'] ?>/edit" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <!-- Form xóa cảnh báo: class="delete-form d-inline" để JS confirm trước khi submit -->
                        <form method="POST" action="/admin/alerts/<?= $alert['id'] ?>/delete" class="delete-form d-inline">
                            <!-- CSRF token -->
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <!-- btn-sm btn-danger: nút nhỏ đỏ; fa-trash: icon thùng rác -->
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Nhúng partial phân trang
$pagination = $data; require __DIR__ . '/../../partials/pagination.php';
?>
