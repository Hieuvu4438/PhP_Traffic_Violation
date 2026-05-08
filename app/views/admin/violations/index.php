<?php
require __DIR__ . '/../../partials/alerts.php';

function statusBadge(string $status): string {
    return match($status) {
        'pending' => '<span class="badge bg-danger">Chưa xử lý</span>',
        'processed' => '<span class="badge bg-warning text-dark">Đã xử lý</span>',
        'paid' => '<span class="badge bg-success">Đã nộp phạt</span>',
        default => '<span class="badge bg-secondary">'.$status.'</span>',
    };
}

function vehicleTypeLabel(string $type): string {
    return match($type) {
        'car' => 'Ô tô',
        'motorcycle' => 'Xe máy',
        'electric_motorcycle' => 'Xe máy điện',
        default => $type,
    };
}
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý vi phạm</h2>
    <div>
        <a href="/admin/violations/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm vi phạm</a>
        <button type="button" class="btn btn-outline-success ms-1" data-bs-toggle="modal" data-bs-target="#importModal">
            <i class="fas fa-file-csv me-1"></i>Nhập CSV
        </button>
    </div>
</div>

<form method="GET" action="/admin/violations" class="mb-3">
    <div class="input-group" style="max-width:400px">
        <input type="text" name="search" class="form-control" placeholder="Tìm theo biển số..."
               value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
    </div>
</form>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Biển số</th>
                    <th>Loại xe</th>
                    <th>Lỗi vi phạm</th>
                    <th>Địa điểm</th>
                    <th>Thời gian</th>
                    <th>Mức phạt</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-3">Chưa có vi phạm nào.</td></tr>
                <?php else: foreach ($items as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <td><strong><?= htmlspecialchars($v['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <td><?= vehicleTypeLabel($v['vehicle_type']) ?></td>
                    <td><?= htmlspecialchars($v['offense_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['location_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDateTime($v['violation_date']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['fine_amount'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= statusBadge($v['status']) ?></td>
                    <td class="text-end">
                        <a href="/admin/violations/<?= $v['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/violations/<?= $v['id'] ?>/delete" class="delete-form d-inline">
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

<!-- Import CSV Modal -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/admin/violations/import" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
            <div class="modal-header">
                <h5 class="modal-title">Nhập vi phạm từ CSV</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small">File CSV cần có cột: plate_number, vehicle_type, violation_date, offense_id, location_id, status, fine_amount, notes</p>
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i>Nhập</button>
            </div>
        </form>
    </div>
</div>
