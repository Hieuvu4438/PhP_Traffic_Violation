<?php
use App\Core\Session;

function vehicleTypeBadge(string $type): string {
    return match($type) {
        'car' => '<span class="badge bg-primary">Ô tô</span>',
        'motorcycle' => '<span class="badge bg-info">Xe máy</span>',
        'electric_motorcycle' => '<span class="badge bg-success">Xe máy điện</span>',
        default => htmlspecialchars($type, ENT_QUOTES, 'UTF-8'),
    };
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">
            <i class="fas fa-car me-2 text-primary"></i>Phương tiện của tôi
        </h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
            <i class="fas fa-plus me-2"></i>Thêm phương tiện
        </button>
    </div>

    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <a href="/tai-khoan" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left me-1"></i>Quay lại tài khoản
    </a>

    <?php if (empty($vehicles)): ?>
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="fas fa-car fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">Bạn chưa đăng ký phương tiện nào</h4>
                <p class="text-muted">Thêm phương tiện để tra cứu nhanh hơn.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                    <i class="fas fa-plus me-2"></i>Thêm ngay
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($vehicles as $vehicle): ?>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title mb-1">
                                        <?= htmlspecialchars($vehicle['plate_number'], ENT_QUOTES, 'UTF-8') ?>
                                    </h5>
                                    <div><?= vehicleTypeBadge($vehicle['vehicle_type']) ?></div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <button class="dropdown-item"
                                                    onclick="openEditModal(<?= $vehicle['id'] ?>,
                                                        '<?= htmlspecialchars(addslashes($vehicle['plate_number']), ENT_QUOTES, 'UTF-8') ?>',
                                                        '<?= htmlspecialchars(addslashes($vehicle['vehicle_type']), ENT_QUOTES, 'UTF-8') ?>',
                                                        '<?= htmlspecialchars(addslashes($vehicle['brand'] ?? ''), ENT_QUOTES, 'UTF-8') ?>',
                                                        '<?= htmlspecialchars(addslashes($vehicle['model'] ?? ''), ENT_QUOTES, 'UTF-8') ?>')">
                                                <i class="fas fa-edit me-2"></i>Sửa
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form method="POST" action="/tai-khoan/phuong-tien/<?= $vehicle['id'] ?>/delete"
                                                  onsubmit="return confirm('Bạn có chắc muốn xóa phương tiện này?');">
                                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fas fa-trash me-2"></i>Xóa
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </div>

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

                            <hr>
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

<!-- Add Vehicle Modal -->
<div class="modal fade" id="addVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="/tai-khoan/phuong-tien">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Thêm phương tiện</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_plate_number" class="form-label">Biển số xe <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_plate_number" name="plate_number"
                               placeholder="VD: 30A-12345" required>
                    </div>
                    <div class="mb-3">
                        <label for="add_vehicle_type" class="form-label">Loại xe <span class="text-danger">*</span></label>
                        <select class="form-select" id="add_vehicle_type" name="vehicle_type" required>
                            <option value="">-- Chọn loại xe --</option>
                            <option value="car">Ô tô</option>
                            <option value="motorcycle">Xe máy</option>
                            <option value="electric_motorcycle">Xe máy điện</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="add_brand" class="form-label">Hãng xe</label>
                        <input type="text" class="form-control" id="add_brand" name="brand" placeholder="VD: Toyota">
                    </div>
                    <div class="mb-3">
                        <label for="add_model" class="form-label">Mẫu xe</label>
                        <input type="text" class="form-control" id="add_model" name="model" placeholder="VD: Vios">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Vehicle Modal -->
<div class="modal fade" id="editVehicleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" id="editVehicleForm">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(Session::csrfToken()) ?>">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Sửa phương tiện</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
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

<script>
function openEditModal(id, plateNumber, vehicleType, brand, model) {
    document.getElementById('editVehicleForm').action = '/tai-khoan/phuong-tien/' + id + '/edit';
    document.getElementById('edit_plate_number').value = plateNumber;
    document.getElementById('edit_vehicle_type').value = vehicleType;
    document.getElementById('edit_brand').value = brand;
    document.getElementById('edit_model').value = model;
    var editModal = new bootstrap.Modal(document.getElementById('editVehicleModal'));
    editModal.show();
}
</script>
