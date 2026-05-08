<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý cảnh báo giao thông</h2>
    <a href="/admin/alerts/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm cảnh báo</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tiêu đề</th>
                    <th>Loại</th>
                    <th>Hết hạn</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có cảnh báo nào.</td></tr>
                <?php else: foreach ($items as $alert): ?>
                <tr>
                    <td><?= $alert['id'] ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($alert['title'], 60), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                        $alertTypeLabels = [
                            'accident' => 'Tai nạn',
                            'congestion' => 'Ùn tắc',
                            'construction' => 'Công trình',
                            'weather' => 'Thời tiết',
                            'other' => 'Khác',
                        ];
                        $type = $alert['alert_type'] ?? 'other';
                        echo htmlspecialchars($alertTypeLabels[$type] ?? $type, ENT_QUOTES, 'UTF-8');
                        ?>
                    </td>
                    <td><?= !empty($alert['expires_at']) ? htmlspecialchars(\App\Core\Helper::formatDate($alert['expires_at']), ENT_QUOTES, 'UTF-8') : '—' ?></td>
                    <td>
                        <span class="badge <?= $alert['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $alert['status'] ? 'Đang hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($alert['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <a href="/admin/alerts/<?= $alert['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/alerts/<?= $alert['id'] ?>/delete" class="delete-form d-inline">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php $pagination = $data; require __DIR__ . '/../../partials/pagination.php'; ?>
