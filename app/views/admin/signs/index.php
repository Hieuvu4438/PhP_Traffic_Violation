<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý biển báo</h2>
    <a href="/admin/signs/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm biển báo</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Mã</th>
                    <th>Hình ảnh</th>
                    <th>Tên biển báo</th>
                    <th>Nhóm</th>
                    <th>Mô tả</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có biển báo nào.</td></tr>
                <?php else: foreach ($items as $sign): ?>
                <tr>
                    <td><?= $sign['id'] ?></td>
                    <td><code><?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td>
                        <?php if (!empty($sign['image'])): ?>
                            <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-height:50px" class="rounded">
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($sign['group_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($sign['description'] ?? '', 60), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <a href="/admin/signs/<?= $sign['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/signs/<?= $sign['id'] ?>/delete" class="delete-form d-inline">
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
