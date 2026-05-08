<?php
use App\Core\Session;

/**
 * Helper: trả về badge HTML theo loại phương tiện.
 */
function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    };
}
?>

<!--
  Trang quản lý phương tiện của người dùng (client/taikhoan/vehicles.php)
  - Danh sách phương tiện đã đăng ký dạng card 2 cột
  - Mỗi card: biển số, loại xe (badge), hãng xe, mẫu xe, nút tra cứu
  - Dropdown menu: sửa (mở modal), xóa (form POST + confirm)
  - Modal Thêm phương tiện (addVehicleModal)
  - Modal Sửa phương tiện (editVehicleModal) - điền dữ liệu bằng JS
  - CSRF token trong tất cả form POST
-->

<div class="container py-4">
    <!-- Header: tiêu đề + nút Thêm -->
    <!-- d-flex justify-content-between align-items-center: tiêu đề trái + nút phải -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <!-- fa-car: icon xe ô tô -->
            <i class="fas fa-car me-2 text-primary"></i>Phương tiện của tôi
        </h2>
        <!-- data-bs-toggle="modal" + data-bs-target="#addVehicleModal": mở modal thêm xe -->
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <!-- fa-plus: icon dấu cộng (thêm mới) -->
            <i class="fas fa-plus me-2"></i>Thêm phương tiện
        </button>
    </div>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- Nút quay lại dashboard tài khoản -->
    <a href="/tai-khoan" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i>Quay lại tài khoản
    </a>

    <?php if (empty($vehicles)): ?>
        <!-- Trạng thái rỗng: chưa có phương tiện -->
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-car fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Bạn chưa đăng ký phương tiện nào</h4>
                <p class="text-muted">Thêm phương tiện để tra cứu nhanh hơn.</p>
                <!-- Mở modal thêm (giống nút trên) -->
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                    <i class="fas fa-plus me-2"></i>Thêm ngay
                </button>
            </div>
        </div>
    <?php else: ?>
        <!-- Danh sách phương tiện: lưới 2 cột desktop (col-md-6) -->
        <div class="row g-4">
            <?php foreach ($vehicles as $vehicle): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <!-- Header card: biển số + loại xe + dropdown menu -->
                            <!-- d-flex justify-content-between: thông tin trái + dropdown phải -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <?= htmlspecialchars($vehicle['plate_number'], ENT_QUOTES, 'UTF-8') ?>
                                    </h5>
                                    <div><?= vehicleTypeBadge($vehicle['vehicle_type']) ?></div>
                                </div>
                                <!-- Dropdown menu 3 chấm (ellipsis-v) -->
                                <div class="dropdown">
                                    <!-- btn-outline-secondary: nút viền xám -->
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                        <!-- fa-ellipsis-v: icon 3 chấm dọc -->
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <!-- dropdown-menu-end: menu đổ sang trái (không tràn màn hình) -->
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <!-- Gọi JS function openEditModal() với dữ liệu phương tiện -->
                                            <!-- addslashes: escape dấu nháy để JS string không bị vỡ -->
                                            <button class="dropdown-item"
                                                    onclick="openEditModal(<?= $vehicle['id'] ?>,
                                                        '<?= htmlspecialchars(addslashes($vehicle['plate_number']), ENT_QUOTES, 'UTF-8') ?>',
                                                        '<?= htmlspecialchars(addslashes($vehicle['vehicle_type']), ENT_QUOTES, 'UTF-8') ?>',
                                                        '<?= htmlspecialchars(addslashes($vehicle['brand'] ?? ''), ENT_QUOTES, 'UTF-8') ?>',
                                                        '<?= htmlspecialchars(addslashes($vehicle['model'] ?? ''), ENT_QUOTES, 'UTF-8') ?>')">
                                                <!-- fa-edit: icon bút chì (sửa) -->
                                                <i class="fas fa-edit me-2"></i>Sửa
                                            </button>
                                        </li>
                                        <!-- dropdown-divider: đường phân cách trong dropdown -->
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <!-- Form xóa: POST đến /tai-khoan/phuong-tien/ID/delete -->
                                            <!-- onsubmit="confirm()": xác nhận trước khi xóa -->
                                            <form method="POST" action="/tai-khoan/phuong-tien/<?= $vehicle['id'] ?>/delete"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa phương tiện này?');">
                                                <!-- CSRF token trong form xóa -->
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
                                                <!-- text-danger: chữ đỏ cho nút xóa -->
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <!-- fa-trash: icon thùng rác (xóa) -->
                                                    <i class="fas fa-trash me-2"></i>Xóa
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Thông tin chi tiết: hãng xe + mẫu xe -->
                            <!-- row g-2 text-muted small: grid gap 0.5rem, chữ xám, nhỏ -->
                            <div class="row g-2 text-muted small">
                                <?php if (!empty($vehicle['brand'])): ?>
                                    <div class="col-6">
                                        <strong>Hãng xe:</strong>
                                        <?= htmlspecialchars($vehicle['brand'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($vehicle['model'])): ?>
                                    <div class="col-6">
                                        <strong>Mẫu xe:</strong>
                                        <?= htmlspecialchars($vehicle['model'], ENT_QUOTES, 'UTF-8') ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Đường kẻ phân cách + nút tra cứu nhanh -->
                            <hr>
                            <!-- w-100: full width -->
                            <a href="/tra-cuu" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-search me-1"></i>Tra cứu vi phạm
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!--
  === Modal Thêm phương tiện (Add) ===
  - modal fade: hiệu ứng fade in/out khi mở/đóng
  - tabindex="-1": không nhận focus khi tab
  - id="addVehicleModal": khớp với data-bs-target của nút mở
-->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <!-- modal-dialog: căn giữa modal -->
    <div class="modal-dialog">
        <!-- modal-content: nội dung modal -->
        <div class="modal-content">
            <!-- Form POST đến /tai-khoan/phuong-tien (thêm mới) -->
            <form method="POST" action="/tai-khoan/phuong-tien">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
                <!-- modal-header bg-primary text-white: header xanh chữ trắng -->
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Thêm phương tiện</h5>
                    <!-- btn-close-white: nút đóng màu trắng (cho nền tối) | data-bs-dismiss="modal": đóng modal -->
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Biển số xe -->
                    <div class="mb-3">
                        <label for="add_plate_number" class="form-label">Biển số xe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_plate_number" name="plate_number"
                               placeholder="VD: 30A-12345" required>
                    </div>
                    <!-- Loại xe (select) -->
                    <div class="mb-3">
                        <label for="add_vehicle_type" class="form-label">Loại xe <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_vehicle_type" name="vehicle_type" required>
                            <option value="">-- Chọn loại xe --</option>
                            <option value="car">Ô tô</option>
                            <option value="motorcycle">Xe máy</option>
                            <option value="electric_motorcycle">Xe máy điện</option>
                        </select>
                    </div>
                    <!-- Hãng xe (không bắt buộc) -->
                    <div class="mb-3">
                        <label for="add_brand" class="form-label">Hãng xe</label>
                        <input type="text" class="form-control" id="add_brand" name="brand" placeholder="VD: Toyota">
                    </div>
                    <!-- Mẫu xe (không bắt buộc) -->
                    <div class="mb-3">
                        <label for="add_model" class="form-label">Mẫu xe</label>
                        <input type="text" class="form-control" id="add_model" name="model" placeholder="VD: Vios">
                    </div>
                </div>
                <!-- modal-footer: nút Đóng + Lưu -->
                <div class="modal-footer">
                    <!-- btn-secondary: nút xám | data-bs-dismiss="modal": đóng modal -->
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <!-- btn-primary: nút xanh | fa-save: icon đĩa mềm (lưu) -->
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--
  === Modal Sửa phương tiện (Edit) ===
  - Cấu trúc tương tự modal Thêm
  - id="editVehicleModal"
  - Form id="editVehicleForm" với action sẽ được set động bằng JS
-->
<div class="modal fade" id="editVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Action sẽ được JS set thành: /tai-khoan/phuong-tien/ID/edit -->
            <form method="POST" id="editVehicleForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
                <!-- modal-header bg-info text-white: header xanh nhạt (phân biệt với Thêm) -->
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Sửa phương tiện</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Inputs sẽ được JS điền giá trị từ phương tiện đã chọn -->
                    <div class="mb-3">
                        <label for="edit_plate_number" class="form-label">Biển số xe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_plate_number" name="plate_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_vehicle_type" class="form-label">Loại xe <span class="text-danger">*</span></label>
                        <select class="form-select" id="edit_vehicle_type" name="vehicle_type" required>
                            <option value="car">Ô tô</option>
                            <option value="motorcycle">Xe máy</option>
                            <option value="electric_motorcycle">Xe máy điện</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_brand" class="form-label">Hãng xe</label>
                        <input type="text" class="form-control" id="edit_brand" name="brand">
                    </div>
                    <div class="mb-3">
                        <label for="edit_model" class="form-label">Mẫu xe</label>
                        <input type="text" class="form-control" id="edit_model" name="model">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!--
  JavaScript: xử lý modal Sửa phương tiện.
  - openEditModal(id, plateNumber, vehicleType, brand, model):
    1. Set action form edit = /tai-khoan/phuong-tien/ID/edit
    2. Điền giá trị vào các input của modal edit
    3. Hiển thị modal bằng bootstrap.Modal
-->
<script>
/**
 * Mở modal sửa phương tiện và điền dữ liệu hiện tại vào form.
 * @param {number} id - ID phương tiện trong DB
 * @param {string} plateNumber - Biển số xe
 * @param {string} vehicleType - Loại xe (car/motorcycle/electric_motorcycle)
 * @param {string} brand - Hãng xe
 * @param {string} model - Mẫu xe
 */
function openEditModal(id, plateNumber, vehicleType, brand, model) {
    // Set action cho form edit
    document.getElementById('editVehicleForm').action = '/tai-khoan/phuong-tien/' + id + '/edit';
    // Điền dữ liệu vào các trường
    document.getElementById('edit_plate_number').value = plateNumber;
    document.getElementById('edit_vehicle_type').value = vehicleType;
    document.getElementById('edit_brand').value = brand;
    document.getElementById('edit_model').value = model;
    // Khởi tạo Bootstrap Modal và hiển thị
    var editModal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
    editModal.show();
}
</script>
