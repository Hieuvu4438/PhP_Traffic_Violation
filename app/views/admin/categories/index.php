<?php
// ==================================================================================
// VIEW: Quản lý danh mục (Admin)
// Hiển thị 2 bảng cạnh nhau (col-md-6): Danh mục tin tức (news_categories) và
// Danh mục lỗi vi phạm (offense_categories). Mỗi loại đều có modal thêm mới (Add)
// và modal sửa (Edit) cho từng mục (tạo id động theo ID của danh mục).
// Dữ liệu: $newsCategories, $offenseCategories (mảng danh mục riêng cho từng loại).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;
?>

<!-- Tiêu đề trang; mb-4: margin-bottom 1.5rem -->
<h2 class="mb-4">Quản lý danh mục</h2>

<!-- row g-4: hàng flex, khoảng cách giữa các cột 1.5rem -->
<div class="row g-4">

    <!-- ========== CỘT TRÁI: Danh mục tin tức (col-md-6 = nửa màn hình desktop) ========== -->
    <div class="col-md-6">
        <!-- card: thẻ chứa bảng -->
        <div class="card">
            <!-- card-header: phần đầu thẻ; d-flex justify-content-between align-items-center: flexbox căn đều 2 bên, căn dọc giữa -->
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh mục tin tức</h5>
                <!-- btn btn-sm btn-primary: nút nhỏ màu xanh; data-bs-toggle="modal" data-bs-target="#addNewsCatModal": mở modal thêm danh mục tin -->
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNewsCatModal">
                    <!-- fa-plus: icon dấu cộng thêm mới -->
                    <i class="fas fa-plus"></i> Thêm
                </button>
            </div>
            <!-- card-body p-0: nội dung thẻ không padding -->
            <div class="card-body p-0">
                <!-- table table-hover mb-0: bảng hover dòng, không margin-bottom -->
                <table class="table table-hover mb-0">
                    <!-- thead table-light: đầu bảng nền xám nhạt -->
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
                        <?php else:
                            foreach ($newsCategories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <!-- code: hiển thị slug dạng monospace -->
                            <td><code><?= htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8') ?></code></td>
                            <td class="text-end">
                                <!-- Nút Sửa mở modal edit riêng cho từng danh mục (id động: editNewsCatModal{id}) -->
                                <!-- btn-sm btn-warning: nút nhỏ màu vàng; fa-edit: icon bút sửa -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editNewsCatModal<?= $cat['id'] ?>" title="Sửa"><i class="fas fa-edit"></i></button>
                                <!-- Form xóa danh mục: có hidden input category_type="news" để controller biết xóa loại nào -->
                                <form method="POST" action="/admin/categories/<?= $cat['id'] ?>/delete" class="delete-form d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                                    <input type="hidden" name="category_type" value="news">
                                    <!-- btn-sm btn-danger: nút nhỏ đỏ; fa-trash: icon thùng rác -->
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

    <!-- ========== CỘT PHẢI: Danh mục lỗi vi phạm (col-md-6) ========== -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Danh mục lỗi vi phạm</h5>
                <!-- data-bs-target="#addOffenseCatModal": mở modal thêm danh mục lỗi vi phạm -->
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
                        <?php else:
                            foreach ($offenseCategories as $cat): ?>
                        <tr>
                            <td><?= $cat['id'] ?></td>
                            <td><?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <!-- Cắt mô tả còn 40 ký tự nếu dài -->
                            <td><?= htmlspecialchars(\App\Core\Helper::truncate($cat['description'] ?? '—', 40), ENT_QUOTES, 'UTF-8') ?></td>
                            <td class="text-end">
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editOffenseCatModal<?= $cat['id'] ?>" title="Sửa"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="/admin/categories/<?= $cat['id'] ?>/delete" class="delete-form d-inline">
                                    <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                                    <!-- category_type="offense": để controller biết xóa danh mục lỗi vi phạm -->
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

<!-- ==================== MODALS ==================== -->

<!-- ===== Modal THÊM danh mục tin tức ===== -->
<!-- modal fade: hộp thoại popup có hiệu ứng mờ dần + trượt; tabindex="-1": không focus bằng Tab -->
<div class="modal fade" id="addNewsCatModal" tabindex="-1">
    <!-- modal-dialog: khung hộp thoại căn giữa -->
    <div class="modal-dialog">
        <form method="POST" action="/admin/categories" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <!-- category_type="news" để controller biết tạo danh mục loại nào -->
            <input type="hidden" name="category_type" value="news">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục tin tức</h5>
                <!-- btn-close: nút X đóng modal; data-bs-dismiss="modal": thuộc tính Bootstrap 5 để đóng modal -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Tên danh mục</label>
                <!-- form-control: input Bootstrap; required: bắt buộc nhập -->
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="modal-footer">
                <!-- btn-secondary: nút xám Hủy; data-bs-dismiss="modal": đóng modal -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <!-- btn-primary: nút xanh Lưu -->
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- ===== Modal THÊM danh mục lỗi vi phạm ===== -->
<div class="modal fade" id="addOffenseCatModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" action="/admin/categories" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <!-- category_type="offense" -->
            <input type="hidden" name="category_type" value="offense">
            <div class="modal-header">
                <h5 class="modal-title">Thêm danh mục lỗi vi phạm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- mb-3: margin-bottom 1rem -->
                <div class="mb-3">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mô tả</label>
                    <!-- rows="2": textarea cao 2 dòng -->
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

<!-- ===== Modal SỬA cho từng danh mục tin tức (lặp qua $newsCategories) ===== -->
<!-- Mỗi modal có id riêng: editNewsCatModal{id} để tránh trùng lặp -->
<?php foreach ($newsCategories as $cat): ?>
<div class="modal fade" id="editNewsCatModal<?= $cat['id'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <!-- Action đến /admin/categories/{id}, controller xử lý update -->
        <form method="POST" action="/admin/categories/<?= $cat['id'] ?>" class="modal-content">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <input type="hidden" name="category_type" value="news">
            <div class="modal-header">
                <h5 class="modal-title">Sửa danh mục tin tức</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label">Tên danh mục</label>
                <!-- Giá trị hiện tại được đổ vào input để người dùng sửa -->
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

<!-- ===== Modal SỬA cho từng danh mục lỗi vi phạm (lặp qua $offenseCategories) ===== -->
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
