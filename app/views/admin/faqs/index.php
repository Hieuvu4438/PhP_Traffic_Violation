<?php
// ==================================================================================
// VIEW: Quản lý FAQ (Admin) - Danh sách
// Hiển thị bảng danh sách câu hỏi thường gặp: ID, câu hỏi, danh mục, thứ tự sắp xếp,
// trạng thái (Hiển thị/Ẩn), kèm nút Sửa/Xóa và phân trang.
// Dữ liệu: $items (danh sách FAQ), $data (thông tin phân trang).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Hàng tiêu đề + nút Thêm FAQ ========== -->
<!-- d-flex...align-items-center: flexbox căn đều 2 bên trái-phải, căn dọc giữa; mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý FAQ</h2>
    <!-- btn btn-primary: nút xanh dương; fa-plus me-1: icon dấu cộng + margin-right 0.25rem -->
    <a href="/admin/faqs/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm FAQ</a>
</div>

<!-- ========== Bảng danh sách FAQ ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ không padding -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng hover dòng, không margin-bottom -->
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
                    <!-- colspan 6: gộp 6 cột; text-center text-muted py-3: căn giữa, chữ xám, padding trên/dưới 1rem -->
                    <tr><td colspan="6" class="text-center text-muted py-3">Chưa có FAQ nào.</td></tr>
                <?php else:
                    foreach ($items as $faq): ?>
                <tr>
                    <td><?= $faq['id'] ?></td>
                    <!-- Cắt câu hỏi còn 80 ký tự nếu dài -->
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($faq['question'], 80), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($faq['category'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Thứ tự sắp xếp: số càng nhỏ càng hiển thị trước -->
                    <td><?= $faq['sort_order'] ?></td>
                    <td>
                        <!-- Badge trạng thái:
                             status=1 => bg-success (xanh lá): Hiển thị
                             status=0 => bg-secondary (xám): Ẩn -->
                        <span class="badge <?= $faq['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $faq['status'] ? 'Hiển thị' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <!-- Nút Sửa: btn-sm = nút nhỏ, btn-warning = vàng, fa-edit = icon bút sửa -->
                        <a href="/admin/faqs/<?= $faq['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <!-- Form xóa FAQ: class="delete-form d-inline" để JS confirm trước khi submit -->
                        <form method="POST" action="/admin/faqs/<?= $faq['id'] ?>/delete" class="delete-form d-inline">
                            <!-- CSRF token -->
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
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

<?php
// Nhúng partial phân trang
$pagination = $data; require __DIR__ . '/../../partials/pagination.php';
?>
