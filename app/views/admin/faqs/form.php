<?php
require __DIR__ . '/../../partials/alerts.php';
use App\Core\Session;
$isEdit = isset($faq);
$old = $isEdit ? $faq : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= $isEdit ? '/admin/faqs/' . $faq['id'] : '/admin/faqs' ?>">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label">Câu hỏi <span class="text-danger">*</span></label>
                    <input type="text" name="question" class="form-control <?= isset($errors['question']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['question'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['question'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['question'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-12">
                    <label class="form-label">Câu trả lời <span class="text-danger">*</span></label>
                    <textarea name="answer" class="form-control <?= isset($errors['answer']) ? 'is-invalid' : '' ?>" rows="6"><?= htmlspecialchars($old['answer'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (isset($errors['answer'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['answer'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Danh mục</label>
                    <input type="text" name="category" class="form-control"
                           value="<?= htmlspecialchars($old['category'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="VD: Tra cứu, Xử phạt">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Thứ tự</label>
                    <input type="number" name="sort_order" class="form-control"
                           value="<?= htmlspecialchars((string)($old['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" <?= (int)($old['status'] ?? 1) === 1 ? 'selected' : '' ?>>Hiển thị</option>
                        <option value="0" <?= (int)($old['status'] ?? 1) === 0 ? 'selected' : '' ?>>Ẩn</option>
                    </select>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                <a href="/admin/faqs" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a>
            </div>
        </form>
    </div>
</div>
