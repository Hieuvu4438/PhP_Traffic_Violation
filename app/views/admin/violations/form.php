<?php
// ==================================================================================
// VIEW: Form Thêm/Sửa vi phạm (Admin)
// Dùng chung cho thêm mới và chỉnh sửa. Form có select offense_id (lỗi vi phạm)
// và select location_id (địa điểm) được populate từ controller.
// Dữ liệu truyền vào: $title, $violation (khi sửa), $offenses (danh sách lỗi vi phạm), $locations (danh sách địa điểm)
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;

// Xác định chế độ: isset($violation) => đang sửa
$isEdit = isset($violation);
// Lấy dữ liệu cũ: từ $violation (nếu sửa) hoặc old_input từ session (nếu thêm mới bị lỗi)
$old = $isEdit ? $violation : (Session::get('old_input') ?? []);
// Lấy lỗi validation từ session
$errors = Session::get('form_errors') ?? [];
// Xóa khỏi session để không hiển thị lại
Session::remove('form_errors');
Session::remove('old_input');
?>

<!-- Tiêu đề trang, mb-4: margin-bottom 1.5rem -->
<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <!-- Form POST: nếu sửa thì action đến /admin/violations/{id}, thêm mới thì đến /admin/violations -->
        <form method="POST" action="<?= $isEdit ? '/admin/violations/' . $violation['id'] : '/admin/violations' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <!-- row g-3: hàng flex, khoảng cách cột 1rem -->
            <div class="row g-3">
                <!-- ===== Biển số xe (col-md-6) ===== -->
                <div class="col-md-6">
                    <!-- text-danger: dấu * màu đỏ cho trường bắt buộc -->
                    <label class="form-label">Plate Number <span class="text-danger">*</span></label>
                    <!-- form-control: input Bootstrap; is-invalid: viền đỏ khi có lỗi -->
                    <input type="text" name="plate_number" class="form-control <?= isset($errors['plate_number']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['plate_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="e.g., 30A-12345">
                    <?php if (isset($errors['plate_number'])): ?>
                        <!-- invalid-feedback: dòng chữ đỏ hiển thị lỗi đầu tiên -->
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['plate_number'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Loại xe (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Vehicle Type <span class="text-danger">*</span></label>
                    <!-- form-select: dropdown Bootstrap -->
                    <select name="vehicle_type" class="form-select <?= isset($errors['vehicle_type']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Select vehicle type --</option>
                        <!-- Lưu ý: giá trị lưu trong DB là tiếng Anh (car/motorcycle/electric_motorcycle), label hiển thị là tiếng Việt -->
                        <option value="car" <?= ($old['vehicle_type'] ?? '') === 'car' ? 'selected' : '' ?>>Car</option>
                        <option value="motorcycle" <?= ($old['vehicle_type'] ?? '') === 'motorcycle' ? 'selected' : '' ?>>Motorcycle</option>
                        <option value="electric_motorcycle" <?= ($old['vehicle_type'] ?? '') === 'electric_motorcycle' ? 'selected' : '' ?>>Electric Motorcycle</option>
                    </select>
                    <?php if (isset($errors['vehicle_type'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['vehicle_type'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Thời gian vi phạm (col-md-6) ===== -->
                <div class="col-md-6">
                    <label class="form-label">Violation Date <span class="text-danger">*</span></label>
                    <!-- type="datetime-local": input chọn ngày + giờ của trình duyệt -->
                    <input type="datetime-local" name="violation_date" class="form-control <?= isset($errors['violation_date']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['violation_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['violation_date'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['violation_date'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Lỗi vi phạm (col-md-6) - select từ danh sách offenses ===== -->
                <div class="col-md-6">
                    <label class="form-label">Offense</label>
                    <select name="offense_id" class="form-select <?= isset($errors['offense_id']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Select offense --</option>
                        <?php foreach ($offenses as $o): ?>
                            <!-- So sánh == (không strict) vì old['offense_id'] có thể là string, $o['id'] là int -->
                            <option value="<?= $o['id'] ?>" <?= ($old['offense_id'] ?? '') == $o['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($o['name'], ENT_QUOTES, 'UTF-8') ?>
                                <!-- Hiển thị tên danh mục trong ngoặc nếu có -->
                                <?= !empty($o['category_name']) ? ' (' . htmlspecialchars($o['category_name'], ENT_QUOTES, 'UTF-8') . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ===== Địa điểm (col-md-6) - select từ danh sách locations ===== -->
                <div class="col-md-6">
                    <label class="form-label">Location</label>
                    <select name="location_id" class="form-select <?= isset($errors['location_id']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Select location --</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>" <?= ($old['location_id'] ?? '') == $loc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ===== Trạng thái (col-md-3) ===== -->
                <div class="col-md-3">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">
                        <!-- Mặc định là 'pending' (chưa xử lý) -->
                        <option value="pending" <?= ($old['status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="processed" <?= ($old['status'] ?? '') === 'processed' ? 'selected' : '' ?>>Processed</option>
                        <option value="paid" <?= ($old['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid</option>
                    </select>
                </div>

                <!-- ===== Mức phạt (col-md-3) ===== -->
                <div class="col-md-3">
                    <label class="form-label">Fine Amount</label>
                    <input type="text" name="fine_amount" class="form-control"
                           value="<?= htmlspecialchars($old['fine_amount'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- ===== Số quyết định (col-md-3) ===== -->
                <div class="col-md-3">
                    <label class="form-label">Decision Number</label>
                    <input type="text" name="decision_number" class="form-control"
                           value="<?= htmlspecialchars($old['decision_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- ===== Ngày ra quyết định (col-md-3) ===== -->
                <div class="col-md-3">
                    <label class="form-label">Decision Date</label>
                    <!-- type="date": input chọn ngày -->
                    <input type="date" name="decision_date" class="form-control"
                           value="<?= htmlspecialchars($old['decision_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- ===== Ghi chú (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <!-- rows="3": textarea cao 3 dòng -->
                    <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <!-- ===== Nút Lưu và Quay lại ===== -->
            <!-- mt-4: margin-top 1.5rem -->
            <div class="mt-4">
                <!-- btn-primary: nút xanh; fa-save: icon đĩa mềm lưu; me-1: margin-right 0.25rem -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                <!-- btn-secondary: nút xám; fa-arrow-left: icon mũi tên trái quay lại -->
                <a href="/admin/violations" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </form>
    </div>
</div>
