<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý FAQ</h2>
    <a href="/admin/faqs/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm FAQ</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Câu hỏi</th>
                    <th>Danh mục</th>
                    <th>Thứ tự</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-3">Chưa có FAQ nào.</td></tr>
                <?php else: foreach ($items as $faq): ?>
                <tr>
                    <td><?= $faq['id'] ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($faq['question'], 80), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($faq['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= $faq['sort_order'] ?></td>
                    <td>
                        <span class="badge <?= $faq['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $faq['status'] ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <a href="/admin/faqs/<?= $faq['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/faqs/<?= $faq['id'] ?>/delete" class="delete-form d-inline">
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
