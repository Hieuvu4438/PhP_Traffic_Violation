<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý tin nhắn liên hệ</h2>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Người gửi</th>
                    <th>Email</th>
                    <th>Tiêu đề</th>
                    <th>Trạng thái</th>
                    <th>Ngày gửi</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có tin nhắn nào.</td></tr>
                <?php else: foreach ($items as $msg): ?>
                <tr class="<?= !$msg['is_read'] ? 'fw-bold' : '' ?>">
                    <td><?= $msg['id'] ?></td>
                    <td><?= htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($msg['subject'] ?? '—', 50), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge <?= $msg['is_read'] ? 'bg-success' : 'bg-danger' ?>">
                            <?= $msg['is_read'] ? 'Đã đọc' : 'Chưa đọc' ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($msg['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#viewMsg<?= $msg['id'] ?>" title="Xem"><i class="fas fa-eye"></i></button>
                        <?php if (!$msg['is_read']): ?>
                        <form method="POST" action="/admin/messages/<?= $msg['id'] ?>/read" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <button type="submit" class="btn btn-sm btn-success" title="Đánh dấu đã đọc"><i class="fas fa-check"></i></button>
                        </form>
                        <?php endif; ?>
                        <form method="POST" action="/admin/messages/<?= $msg['id'] ?>/delete" class="delete-form d-inline">
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

<!-- Message detail modals -->
<?php foreach ($items as $msg): ?>
<div class="modal fade" id="viewMsg<?= $msg['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết tin nhắn #<?= $msg['id'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Người gửi:</strong> <?= htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Tiêu đề:</strong> <?= htmlspecialchars($msg['subject'] ?? 'Không có', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Ngày gửi:</strong> <?= htmlspecialchars(\App\Core\Helper::formatDateTime($msg['created_at']), ENT_QUOTES, 'UTF-8') ?></p>
                <hr>
                <p><strong>Nội dung:</strong></p>
                <div class="border rounded p-3 bg-light"><?= nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')) ?></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <?php if (!$msg['is_read']): ?>
                <form method="POST" action="/admin/messages/<?= $msg['id'] ?>/read">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                    <button type="submit" class="btn btn-success">Đánh dấu đã đọc</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
