<?php
require __DIR__ . '/../../partials/alerts.php';
use App\Core\Session;
$isEdit = isset($violation);
$old = $isEdit ? $violation : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '/admin/violations/' . $violation['id'] : '/admin/violations' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Biển số xe <span class="text-danger">*</span></label>
                    <input type="text" name="plate_number" class="form-control <?= isset($errors['plate_number']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['plate_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="VD: 30A-12345">
                    <?php if (isset($errors['plate_number'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['plate_number'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Loại xe <span class="text-danger">*</span></label>
                    <select name="vehicle_type" class="form-select <?= isset($errors['vehicle_type']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Chọn loại xe --</option>
                        <option value="car" <?= ($old['vehicle_type'] ?? '') === 'car' ? 'selected' : '' ?>>Ô tô</option>
                        <option value="motorcycle" <?= ($old['vehicle_type'] ?? '') === 'motorcycle' ? 'selected' : '' ?>>Xe máy</option>
                        <option value="electric_motorcycle" <?= ($old['vehicle_type'] ?? '') === 'electric_motorcycle' ? 'selected' : '' ?>>Xe máy điện</option>
                    </select>
                    <?php if (isset($errors['vehicle_type'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['vehicle_type'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Thời gian vi phạm <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="violation_date" class="form-control <?= isset($errors['violation_date']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['violation_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['violation_date'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['violation_date'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Lỗi vi phạm</label>
                    <select name="offense_id" class="form-select <?= isset($errors['offense_id']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Chọn lỗi vi phạm --</option>
                        <?php foreach ($offenses as $o): ?>
                            <option value="<?= $o['id'] ?>" <?= ($old['offense_id'] ?? '') == $o['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($o['name'], ENT_QUOTES, 'UTF-8') ?>
                                <?= !empty($o['category_name']) ? ' (' . htmlspecialchars($o['category_name'], ENT_QUOTES, 'UTF-8') . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Địa điểm</label>
                    <select name="location_id" class="form-select <?= isset($errors['location_id']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Chọn địa điểm --</option>
                        <?php foreach ($locations as $loc): ?>
                            <option value="<?= $loc['id'] ?>" <?= ($old['location_id'] ?? '') == $loc['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($loc['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Trạng thái <span class="text-danger">*</span></label>
                    <select name="status" class="form-select <?= isset($errors['status']) ? 'is-invalid' : '' ?>">
                        <option value="pending" <?= ($old['status'] ?? 'pending') === 'pending' ? 'selected' : '' ?>>Chưa xử lý</option>
                        <option value="processed" <?= ($old['status'] ?? '') === 'processed' ? 'selected' : '' ?>>Đã xử lý</option>
                        <option value="paid" <?= ($old['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Đã nộp phạt</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Mức phạt</label>
                    <input type="text" name="fine_amount" class="form-control"
                           value="<?= htmlspecialchars($old['fine_amount'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Số quyết định</label>
                    <input type="text" name="decision_number" class="form-control"
                           value="<?= htmlspecialchars($old['decision_number'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Ngày ra quyết định</label>
                    <input type="date" name="decision_date" class="form-control"
                           value="<?= htmlspecialchars($old['decision_date'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">Ghi chú</label>
                    <textarea name="notes" class="form-control" rows="3"><?= htmlspecialchars($old['notes'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="/admin/violations" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
