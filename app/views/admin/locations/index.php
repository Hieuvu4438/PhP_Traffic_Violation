<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý địa điểm</h2>
    <a href="/admin/locations/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm địa điểm</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Loại</th>
                    <th>Địa chỉ</th>
                    <th>Tọa độ</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có địa điểm nào.</td></tr>
                <?php else: foreach ($items as $loc): ?>
                <tr>
                    <td><?= $loc['id'] ?></td>
                    <td><?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                        $typeLabels = [
                            'camera' => 'Camera',
                            'csgt' => 'CSGT',
                            'toll' => 'Trạm thu phí',
                            'inspection' => 'Đăng kiểm',
                        ];
                        echo htmlspecialchars($typeLabels[$loc['type']] ?? $loc['type'], ENT_QUOTES, 'UTF-8');
                        ?>
                    </td>
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($loc['address'] ?? '—', 40), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><small><?= htmlspecialchars($loc['latitude'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($loc['longitude'], ENT_QUOTES, 'UTF-8') ?></small></td>
                    <td>
                        <span class="badge <?= $loc['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $loc['status'] ? 'Hoạt động' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="/admin/locations/<?= $loc['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/locations/<?= $loc['id'] ?>/delete" class="delete-form d-inline">
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
