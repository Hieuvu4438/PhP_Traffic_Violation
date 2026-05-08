<?php
// ==================================================================================
// VIEW: Quản lý vi phạm (Admin) - Danh sách
// Hiển thị bảng danh sách vi phạm, có ô tìm kiếm theo biển số, nút Thêm vi phạm,
// nút Nhập CSV (mở modal import), và toggle trạng thái inline (click badge để đổi).
// Dữ liệu: $items (danh sách vi phạm), $data (phân trang), $search (từ khóa tìm kiếm).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

/**
 * Hàm trả về badge HTML tương ứng với trạng thái vi phạm.
 * - pending => badge bg-danger (đỏ): Chưa xử lý
 * - processed => badge bg-warning text-dark (vàng chữ đen): Đã xử lý
 * - paid => badge bg-success (xanh lá): Đã nộp phạt
 * - default => badge bg-secondary (xám): hiển thị nguyên trạng thái
 */
function statusBadge(string $status): string {
    return match($status) {
        'pending' => '<span class="badge bg-danger">Chưa xử lý</span>',
        'processed' => '<span class="badge bg-warning text-dark">Đã xử lý</span>',
        'paid' => '<span class="badge bg-success">Đã nộp phạt</span>',
        default => '<span class="badge bg-secondary">'.$status.'</span>',
    };
}

/**
 * Hàm trả về nhãn tiếng Việt cho loại phương tiện.
 * - car => Ô tô
 * - motorcycle => Xe máy
 * - electric_motorcycle => Xe máy điện
 */
function vehicleTypeLabel(string $type): string {
    return match($type) {
        'car' => 'Ô tô',
        'motorcycle' => 'Xe máy',
        'electric_motorcycle' => 'Xe máy điện',
        default => $type,
    };
}
?>

<!-- ========== Hàng tiêu đề + 2 nút (Thêm vi phạm và Nhập CSV) ========== -->
<!-- d-flex...align-items-center: flexbox căn đều 2 bên, căn dọc giữa, mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý vi phạm</h2>
    <div>
        <!-- btn btn-primary: nút xanh dương; fa-plus: icon dấu cộng thêm mới; me-1: margin-right 0.25rem -->
        <a href="/admin/violations/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Thêm vi phạm</a>
        <!-- btn-outline-success: nút viền xanh lá; ms-1: margin-left 0.25rem -->
        <!-- data-bs-toggle="modal" data-bs-target="#importModal": mở modal #importModal khi click (Bootstrap 5) -->
        <button type="button" class="btn btn-outline-success ms-1" data-bs-toggle="modal" data-bs-target="#importModal">
            <!-- fa-file-csv: icon file CSV -->
            <i class="fas fa-file-csv me-1"></i>Nhập CSV
        </button>
    </div>
</div>

<!-- ========== Ô tìm kiếm theo biển số ========== -->
<!-- Form GET, giữ nguyên tham số search trên URL để phân trang hoạt động đúng -->
<form method="GET" action="/admin/violations" class="mb-3">
    <!-- input-group: nhóm input + nút thành 1 khối liền nhau; style="max-width:400px": giới hạn chiều rộng -->
    <div class="input-group" style="max-width:400px">
        <!-- form-control: input Bootstrap -->
        <input type="text" name="search" class="form-control" placeholder="Tìm theo biển số..."
               value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8') ?>">
        <!-- btn btn-outline-secondary: nút viền xám; fa-search: icon kính lúp tìm kiếm -->
        <button class="btn btn-outline-secondary" type="submit"><i class="fas fa-search"></i></button>
    </div>
</form>

<!-- ========== Bảng danh sách vi phạm ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ không padding -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng có hover dòng, không margin-bottom -->
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Biển số</th>
                    <th>Loại xe</th>
                    <th>Lỗi vi phạm</th>
                    <th>Địa điểm</th>
                    <th>Thời gian</th>
                    <th>Mức phạt</th>
                    <th>Trạng thái</th>
                    <!-- text-end: căn phải -->
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <!-- colspan 9: gộp 9 cột -->
                    <tr><td colspan="9" class="text-center text-muted py-3">Chưa có vi phạm nào.</td></tr>
                <?php else:
                    // Lặp qua danh sách vi phạm
                    foreach ($items as $v): ?>
                <tr>
                    <td><?= $v['id'] ?></td>
                    <!-- Biển số: in đậm, escape XSS -->
                    <td><strong><?= htmlspecialchars($v['plate_number'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                    <!-- Gọi hàm chuyển đổi loại xe sang tiếng Việt -->
                    <td><?= vehicleTypeLabel($v['vehicle_type']) ?></td>
                    <td><?= htmlspecialchars($v['offense_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['location_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDateTime($v['violation_date']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($v['fine_amount'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <!-- Badge trạng thái có thể click để toggle (gửi AJAX):
                             - class="badge border-0": badge không viền
                             - cursor-pointer, style="cursor:pointer": hiển thị con trỏ tay khi hover
                             - data-id, data-type="violation", data-current-status: dữ liệu cho JS xử lý toggle -->
                        <button type="button" class="badge border-0 toggle-status-btn"
                                style="cursor:pointer"
                                data-id="<?= $v['id'] ?>"
                                data-type="violation"
                                data-current-status="<?= $v['status'] ?>"
                                title="Click để đổi trạng thái">
                            <?= statusBadge($v['status']) ?>
                        </button>
                    </td>
                    <td class="text-end">
                        <!-- Nút Sửa: btn-sm = nút nhỏ, btn-warning = vàng, fa-edit = bút sửa -->
                        <a href="/admin/violations/<?= $v['id'] ?>/edit" class="btn btn-sm btn-warning" title="Sửa"><i class="fas fa-edit"></i></a>
                        <!-- Form xóa vi phạm -->
                        <form method="POST" action="/admin/violations/<?= $v['id'] ?>/delete" class="delete-form d-inline">
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

<!-- ========== Modal Nhập CSV ========== -->
<!-- modal fade: hộp thoại popup có hiệu ứng mờ dần + trượt; tabindex="-1": không focus được bằng Tab -->
<div class="modal fade" id="importModal" tabindex="-1">
    <!-- modal-dialog: khung hộp thoại, căn giữa màn hình -->
    <div class="modal-dialog">
        <!-- Form POST tới route import, enctype="multipart/form-data" để upload file CSV -->
        <form method="POST" action="/admin/violations/import" enctype="multipart/form-data" class="modal-content">
            <!-- CSRF token -->
            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">

            <!-- modal-header: phần đầu modal (tiêu đề + nút đóng) -->
            <div class="modal-header">
                <!-- modal-title: tiêu đề hộp thoại -->
                <h5 class="modal-title">Nhập vi phạm từ CSV</h5>
                <!-- btn-close: nút X đóng modal; data-bs-dismiss="modal": đóng modal khi click -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- modal-body: nội dung chính của modal -->
            <div class="modal-body">
                <!-- text-muted small: chữ xám, kích thước nhỏ; mô tả cấu trúc file CSV cần có -->
                <p class="text-muted small">File CSV cần có cột: plate_number, vehicle_type, violation_date, offense_id, location_id, status, fine_amount, notes</p>
                <!-- form-control: input file Bootstrap; accept=".csv": chỉ chấp nhận file CSV; required: bắt buộc -->
                <input type="file" name="csv_file" class="form-control" accept=".csv" required>
            </div>

            <!-- modal-footer: phần chân modal (nút Hủy + Nhập) -->
            <div class="modal-footer">
                <!-- btn btn-secondary: nút xám; data-bs-dismiss="modal": đóng modal -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <!-- btn btn-success: nút xanh lá; fa-upload: icon upload -->
                <button type="submit" class="btn btn-success"><i class="fas fa-upload me-1"></i>Nhập</button>
            </div>
        </form>
    </div>
</div>
