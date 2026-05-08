<?php
// ==================================================================================
// VIEW: Quản lý tin tức (Admin) - Danh sách
// Hiển thị bảng danh sách tin tức với thumbnail, tiêu đề (link đến form sửa),
// danh mục, trạng thái (Đã đăng / Bản nháp), lượt xem, ngày tạo, và các nút thao tác.
// Dữ liệu: $items (danh sách tin tức), $data (thông tin phân trang).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Hàng tiêu đề + nút Thêm tin tức ========== -->
<!-- d-flex...align-items-center: flexbox căn đều 2 bên trái-phải, căn dọc giữa; mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý tin tức</h2>
    <!-- btn btn-primary: nút xanh; fa-plus me-1: icon dấu cộng + margin-right 0.25rem -->
    <a href="/admin/news/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm tin tức</a>
</div>

<!-- ========== Bảng danh sách tin tức ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ không padding -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng có hover dòng, không margin-bottom -->
        <table class="table table-hover mb-0">
            <!-- thead table-light: đầu bảng nền xám nhạt -->
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
                    <!-- colspan 8: gộp 8 cột; text-center text-muted py-3: căn giữa, chữ xám, padding trên/dưới 1rem -->
                    <tr><td colspan="8" class="text-center text-muted py-3">Chưa có tin tức nào.</td></tr>
                <?php else:
                    foreach ($items as $news): ?>
                <tr>
                    <td><?= $news['id'] ?></td>
                    <td>
                        <?php if (!empty($news['thumbnail'])): ?>
                            <!-- Hiển thị ảnh thumbnail:
                                 style: width 60px, height 40px, object-fit:cover cắt ảnh vừa khung không méo;
                                 class="rounded": bo góc ảnh -->
                            <img src="/<?= htmlspecialchars($news['thumbnail'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="width:60px;height:40px;object-fit:cover" class="rounded">
                        <?php else: ?>
                            <!-- Không có thumbnail: hiển thị dấu gạch ngang mờ -->
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <!-- Tiêu đề là link đến form sửa (cắt còn 50 ký tự nếu dài):
                             text-decoration-none: bỏ gạch chân link -->
                        <a href="/admin/news/<?= $news['id'] ?>/edit" class="text-decoration-none">
                            <?= htmlspecialchars(\App\Core\Helper::truncate($news['title'], 50), ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </td>
                    <td><?= htmlspecialchars($news['category_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <!-- Badge trạng thái:
                             status='published' => bg-success (xanh lá): Đã đăng
                             status='draft' => bg-secondary (xám): Bản nháp -->
                        <span class="badge <?= $news['status'] === 'published' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $news['status'] === 'published' ? 'Đã đăng' : 'Bản nháp' ?>
                        </span>
                    </td>
                    <td><?= $news['views'] ?></td>
                    <!-- formatDate(): định dạng ngày tạo -->
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($news['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <!-- Nút Sửa: btn-sm = nút nhỏ, btn-warning = vàng, fa-edit: icon bút sửa -->
                        <a href="/admin/news/<?= $news['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <!-- Form xóa tin tức: class="delete-form d-inline" để JS confirm trước khi xóa -->
                        <form method="POST" action="/admin/news/<?= $news['id'] ?>/delete" class="delete-form d-inline">
                            <!-- CSRF token -->
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <!-- btn-danger: nút đỏ; fa-trash: icon thùng rác -->
                            <button type="submit" class="btn btn-sm btn-danger" title="Xóa"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Nhúng partial phân trang
$pagination = $data; require __DIR__ . '/../../partials/pagination.php';
?>
