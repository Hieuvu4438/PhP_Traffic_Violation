<?php
// ==================================================================================
// VIEW: Quản lý biển báo giao thông (Admin) - Danh sách
// Hiển thị bảng danh sách biển báo: mã (sign_code), hình ảnh, tên, nhóm, mô tả,
// cùng các nút Sửa / Xóa. Có phân trang.
// Dữ liệu: $items (danh sách biển báo), $data (thông tin phân trang).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Hàng tiêu đề + nút Thêm biển báo ========== -->
<!-- d-flex...align-items-center: flexbox căn đều 2 bên trái-phải, căn dọc giữa; mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý biển báo</h2>
    <!-- btn btn-primary: nút xanh dương; fa-plus me-1: icon dấu cộng + margin-right 0.25rem -->
    <a href="/admin/signs/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm biển báo</a>
</div>

<!-- ========== Bảng danh sách biển báo ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ không padding -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng hover dòng, không margin-bottom -->
        <table class="table table-hover mb-0">
            <!-- thead table-light: đầu bảng nền xám nhạt -->
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
                    <!-- colspan 7: gộp 7 cột; text-center text-muted py-3: căn giữa, chữ xám, padding trên/dưới 1rem -->
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có biển báo nào.</td></tr>
                <?php else:
                    // Lặp qua danh sách biển báo
                    foreach ($items as $sign): ?>
                <tr>
                    <td><?= $sign['id'] ?></td>
                    <!-- code: hiển thị mã biển báo dạng monospace (VD: P.101) -->
                    <td><code><?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?></code></td>
                    <td>
                        <?php if (!empty($sign['image'])): ?>
                            <!-- Hiển thị ảnh biển báo đã upload:
                                 max-height:50px: giới hạn chiều cao 50px; rounded: bo góc -->
                            <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-height:50px" class="rounded">
                        <?php else: ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Tên nhóm biển báo (từ JOIN với traffic_sign_groups) -->
                    <td><?= htmlspecialchars($sign['group_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Cắt mô tả còn 60 ký tự -->
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($sign['description'] ?? '', 60), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <!-- Nút Sửa: btn-sm = nút nhỏ, btn-warning = vàng, fa-edit = icon bút sửa -->
                        <a href="/admin/signs/<?= $sign['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <!-- Form xóa biển báo: class="delete-form d-inline" để JS confirm trước khi submit -->
                        <form method="POST" action="/admin/signs/<?= $sign['id'] ?>/delete" class="delete-form d-inline">
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
