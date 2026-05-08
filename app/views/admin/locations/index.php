<?php
// ==================================================================================
// VIEW: Quản lý địa điểm (Admin) - Danh sách
// Hiển thị bảng danh sách địa điểm: tên, loại (Camera/CSGT/Trạm thu phí/Đăng kiểm),
// địa chỉ, tọa độ (lat/lng), trạng thái (Hoạt động/Ẩn), kèm nút Sửa/Xóa và phân trang.
// Dữ liệu: $items (danh sách địa điểm), $data (thông tin phân trang).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Hàng tiêu đề + nút Thêm địa điểm ========== -->
<!-- d-flex...align-items-center: flexbox căn đều 2 bên trái-phải, căn dọc giữa; mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý địa điểm</h2>
    <!-- btn btn-primary: nút xanh dương; fa-plus me-1: icon dấu cộng + margin-right 0.25rem -->
    <a href="/admin/locations/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm địa điểm</a>
</div>

<!-- ========== Bảng danh sách địa điểm ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ không padding -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng hover dòng, không margin-bottom -->
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Loại</th>
                    <th>Địa chỉ</th>
                    <th>Tọa độ</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <!-- colspan 7: gộp 7 cột; text-center text-muted py-3: căn giữa, chữ xám, padding trên/dưới 1rem -->
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có địa điểm nào.</td></tr>
                <?php else:
                    foreach ($items as $loc): ?>
                <tr>
                    <td><?= $loc['id'] ?></td>
                    <td><?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <?php
                        // Map giá trị type (tiếng Anh trong DB) sang nhãn tiếng Việt:
                        // camera => Camera, csgt => CSGT, toll => Trạm thu phí, inspection => Đăng kiểm
                        $typeLabels = [
                            'camera' => 'Camera',
                            'csgt' => 'CSGT',
                            'toll' => 'Trạm thu phí',
                            'inspection' => 'Đăng kiểm',
                        ];
                        echo htmlspecialchars($typeLabels[$loc['type']] ?? $loc['type'], ENT_QUOTES, 'UTF-8');
                        ?>
                    </td>
                    <!-- Địa chỉ: cắt còn 40 ký tự nếu dài -->
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($loc['address'] ?? '—', 40), ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Tọa độ: hiển thị dạng "lat, lng" với font nhỏ -->
                    <td><small><?= htmlspecialchars($loc['latitude'], ENT_QUOTES, 'UTF-8') ?>, <?= htmlspecialchars($loc['longitude'], ENT_QUOTES, 'UTF-8') ?></small></td>
                    <td>
                        <!-- Badge trạng thái:
                             status=1 => bg-success (xanh lá): Hoạt động
                             status=0 => bg-secondary (xám): Ẩn -->
                        <span class="badge <?= $loc['status'] ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $loc['status'] ? 'Hoạt động' : 'Ẩn' ?>
                        </span>
                    </td>
                    <td class="text-end">
                        <!-- Nút Sửa: btn-sm = nút nhỏ, btn-warning = vàng, fa-edit = icon bút sửa -->
                        <a href="/admin/locations/<?= $loc['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <!-- Form xóa địa điểm: class="delete-form d-inline" để JS confirm trước khi submit -->
                        <form method="POST" action="/admin/locations/<?= $loc['id'] ?>/delete" class="delete-form d-inline">
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
