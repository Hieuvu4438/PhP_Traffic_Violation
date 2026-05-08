<?php
require __DIR__ . '/../../partials/alerts.php';
use App\Core\Session;
?>

<h2 class="mb-4">Quản lý danh mục</h2>

<div class="row g-4">
    <!-- News Categories -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh mục tin tức</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsCatModal">
                    <i class="fas fa-plus"></i> Thêm
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Slug</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($newsCategories)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Chưa có danh mục nào.</td></tr>
                        <?php else: foreach ($newsCategories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><code><?= htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8') ?></code></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editNewsCatModal<?= $cat['id'] ?>" title="Sửa"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="/admin/categories/<?= $cat['id'] ?>/delete" class="delete-form d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                                    <input type="hidden" name="category_type" value="news">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Offense Categories -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh mục lỗi vi phạm</h5>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addOffenseCatModal">
                    <i class="fas fa-plus"></i> Thêm
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tên</th>
                            <th>Mô tả</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($offenseCategories)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-3">Chưa có danh mục nào.</td></tr>
                        <?php else: foreach ($offenseCategories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars(\App\Core\Helper::truncate($cat['description'] ?? '—', 40), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editOffenseCatModal<?= $cat['id'] ?>" title="Sửa"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="/admin/categories/<?= $cat['id'] ?>/delete" class="delete-form d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                                    <input type="hidden" name="category_type" value="offense">
                                    <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add News Category Modal -->
<div class="modal fade" id="addNewsCatModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/admin/categories" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <input type="hidden" name="category_type" value="news">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục tin tức</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Add Offense Category Modal -->
<div class="modal fade" id="addOffenseCatModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/admin/categories" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <input type="hidden" name="category_type" value="offense">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục lỗi vi phạm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit modals for each news category -->
<?php foreach ($newsCategories as $cat): ?>
<div class="modal fade" id="editNewsCatModal<?= $cat['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/admin/categories/<?= $cat['id'] ?>" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <input type="hidden" name="category_type" value="news">
            <div class="modal-header">
                <h5 class="modal-title">Sửa danh mục tin tức</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Tên danh mục</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>

<!-- Edit modals for each offense category -->
<?php foreach ($offenseCategories as $cat): ?>
<div class="modal fade" id="editOffenseCatModal<?= $cat['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/admin/categories/<?= $cat['id'] ?>" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <input type="hidden" name="category_type" value="offense">
            <div class="modal-header">
                <h5 class="modal-title">Sửa danh mục lỗi vi phạm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control" rows="2"><?= htmlspecialchars($cat['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>
<?php endforeach; ?>
