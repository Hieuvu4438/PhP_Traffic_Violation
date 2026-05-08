<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý người dùng</h2>
    <a href="/admin/users/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm người dùng</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>SĐT</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Chưa có người dùng nào.</td></tr>
                <?php else: foreach ($items as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= $user['role'] === 'admin' ? 'Admin' : 'User' ?>
                        </span>
                    </td>
                    <td>
                        <span class="badge <?= $user['status'] ? 'bg-success' : 'bg-danger' ?>">
                            <?= $user['status'] ? 'Hoạt động' : 'Bị khóa' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($user['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <button type="button" class="btn btn-sm <?= $user['status'] ? 'btn-secondary' : 'btn-success' ?> toggle-status-btn"
                                data-id="<?= $user['id'] ?>"
                                data-type="user"
                                data-current-status="<?= $user['status'] ?>"
                                title="<?= $user['status'] ? 'Khóa' : 'Mở khóa' ?>">
                            <i class="fas <?= $user['status'] ? 'fa-lock' : 'fa-unlock' ?>"></i>
                        </button>
                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/delete" class="delete-form d-inline">
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
