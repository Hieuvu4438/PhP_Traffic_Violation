<?php require __DIR__ . '/../../partials/alerts.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý tin tức</h2>
    <a href="/admin/news/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm tin tức</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Ảnh</th>
                    <th>Tiêu đề</th>
                    <th>Danh mục</th>
                    <th>Trạng thái</th>
                    <th>Lượt xem</th>
                    <th>Ngày tạo</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="8" class="text-center text-muted py-3">Chưa có tin tức nào.</td></tr>
                <?php else: foreach ($items as $news): ?>
                <tr>
                    <td><?= $news['id'] ?></td>
                    <td>
                        <?php if (!empty($news['thumbnail'])): ?>
                            <img src="/<?= htmlspecialchars($news['thumbnail'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:60px;height:40px;object-fit:cover" class="rounded">
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="/admin/news/<?= $news['id'] ?>/edit" class="text-decoration-none">
                            <?= htmlspecialchars(\App\Core\Helper::truncate($news['title'], 50), ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($news['category_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge <?= $news['status'] === 'published' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $news['status'] === 'published' ? 'Đã đăng' : 'Bản nháp' ?>
                        </span>
                    </td>
                    <td><?= $news['views'] ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($news['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <a href="/admin/news/<?= $news['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <form method="POST" action="/admin/news/<?= $news['id'] ?>/delete" class="delete-form d-inline">
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
