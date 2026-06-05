<?php
// ==================================================================================
// VIEW: Form Thêm/Sửa FAQ (Admin)
// Form gồm: câu hỏi, câu trả lời, danh mục (input text tự do), thứ tự sắp xếp, trạng thái.
// Dữ liệu: $title, $faq (khi sửa).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;

// Xác định chế độ: isset($faq) => đang sửa FAQ
$isEdit = isset($faq);
$old = $isEdit ? $faq : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<!-- Tiêu đề trang; mb-4: margin-bottom 1.5rem -->
<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <!-- Form POST: nếu sửa thì đến /admin/faqs/{id}, thêm mới thì đến /admin/faqs -->
        <form method="POST" action="<?= $isEdit ? '/admin/faqs/' . $faq['id'] : '/admin/faqs' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <!-- row g-3: hàng flex, khoảng cách cột 1rem -->
            <div class="row g-3">
                <!-- ===== Câu hỏi (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                    <input type="text" name="question" class="form-control <?= isset($errors['question']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['question'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['question'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['question'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Câu trả lời (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Câu trả lời <span class="text-danger">*</span></label>
                    <!-- rows="6": textarea cao 6 dòng -->
                    <textarea name="answer" class="form-control <?= isset($errors['answer']) ? 'is-invalid' : '' ?>" rows="6"><?= htmlspecialchars($old['answer'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (isset($errors['answer'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['answer'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Danh mục (col-md-4) - input text tự do, VD: "Tra cứu", "Xử phạt" ===== -->
                <div class="col-md-4">
                    <label class="form-label">Danh mục</label>
                    <input type="text" name="category" class="form-control"
                           value="<?= htmlspecialchars($old['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="VD: Tra cứu, Xử phạt">
                </div>

                <!-- ===== Thứ tự sắp xếp (col-md-4) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Thứ tự</label>
                    <!-- type="number": chỉ cho nhập số -->
                    <input type="number" name="sort_order" class="form-control"
                           value="<?= htmlspecialchars((string)($old['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                </div>

                <!-- ===== Trạng thái (col-md-4) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <!-- Mặc định: 1 = Hiển thị -->
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
            </div>

            <!-- ===== Nút Lưu và Quay lại ===== -->
            <!-- mt-4: margin-top 1.5rem -->
            <div class="mt-4">
                <!-- btn-primary: nút xanh; fa-save: icon đĩa mềm lưu; me-1: margin-right 0.25rem -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <!-- btn-secondary: nút xám; fa-arrow-left: icon mũi tên trái quay lại -->
                <a href="/admin/faqs" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
