<?php
// ==================================================================================
// VIEW: Form Thêm/Sửa tin tức (Admin)
// Dùng CKEditor 5 (CDN) để soạn thảo nội dung bài viết.
// Có upload ảnh đại diện (thumbnail). Khi sửa: hiển thị ảnh cũ nếu có.
// Dữ liệu: $title, $news (khi sửa), $categories (danh sách danh mục tin tức).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';

use App\Core\Session;

// Xác định chế độ: isset($news) => đang sửa tin tức
$isEdit = isset($news);
// Lấy dữ liệu cũ từ $news (khi sửa) hoặc old_input từ session (khi thêm mới lỗi)
$old = $isEdit ? $news : (Session::get('old_input') ?? []);
$errors = Session::get('form_errors') ?? [];
Session::remove('form_errors');
Session::remove('old_input');
?>

<!-- Tiêu đề trang; mb-4: margin-bottom 1.5rem -->
<h2 class="mb-4"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></h2>

<div class="card">
    <div class="card-body">
        <!-- enctype="multipart/form-data": bắt buộc để upload file ảnh -->
        <form method="POST" action="<?= $isEdit ? '/admin/news/' . $news['id'] : '/admin/news' ?>" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">

            <!-- row g-3: hàng flex, khoảng cách cột 1rem -->
            <div class="row g-3">
                <!-- ===== Tiêu đề (col-md-8: 8/12 cột trên desktop) ===== -->
                <div class="col-md-8">
                    <label class="form-label">Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>"
                           value="<?= htmlspecialchars($old['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    <?php if (isset($errors['title'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['title'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>

                <!-- ===== Danh mục (col-md-4: 4/12 cột) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select <?= isset($errors['category_id']) ? 'is-invalid' : '' ?>">
                        <option value="">-- Select category --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($old['category_id'] ?? '') == $cat['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- ===== Ảnh đại diện (col-md-4) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Thumbnail</label>
                    <!-- accept="image/*": chỉ chấp nhận file ảnh -->
                    <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    <?php if ($isEdit && !empty($news['thumbnail'])): ?>
                        <!-- Khi đang sửa và có ảnh cũ: hiển thị ảnh hiện tại -->
                        <!-- mt-1: margin-top 0.25rem; rounded: bo góc ảnh; max-height:80px: giới hạn chiều cao -->
                        <div class="mt-1">
                            <img src="/<?= htmlspecialchars($news['thumbnail'], ENT_QUOTES, 'UTF-8') ?>" alt="" style="max-height:80px" class="rounded">
                        </div>
                    <?php endif; ?>
                </div>

                <!-- ===== Trạng thái (col-md-4) ===== -->
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <!-- Mặc định là 'draft' (bản nháp) -->
                        <option value="draft" <?= ($old['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Draft</option>
                        <option value="published" <?= ($old['status'] ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>

                <!-- ===== Nội dung (col-12: full width) ===== -->
                <div class="col-12">
                    <label class="form-label">Content <span class="text-danger">*</span></label>
                    <!-- id="editor": để CKEditor 5 nhắm vào textarea này; rows="15": cao 15 dòng fallback -->
                    <textarea name="content" id="editor" class="form-control <?= isset($errors['content']) ? 'is-invalid' : '' ?>" rows="15"><?= htmlspecialchars($old['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    <?php if (isset($errors['content'])): ?>
                        <div class="invalid-feedback"><?= htmlspecialchars($errors['content'][0], ENT_QUOTES, 'UTF-8') ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ===== Nút Lưu và Quay lại ===== -->
            <!-- mt-4: margin-top 1.5rem -->
            <div class="mt-4">
                <!-- btn-primary: nút xanh dương; fa-save: icon đĩa mềm lưu -->
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Save</button>
                <!-- btn-secondary: nút xám; fa-arrow-left: icon mũi tên trái quay lại -->
                <a href="/admin/news" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i>Back</a>
            </div>
        </form>
    </div>
</div>

<!-- ========== Tải CKEditor 5 từ CDN và khởi tạo ========== -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
// Khởi tạo CKEditor 5 Classic cho textarea #editor
ClassicEditor
    .create(document.querySelector('#editor'), {
        // language: 'vi': giao diện tiếng Việt
        language: 'vi',
        // toolbar: thanh công cụ soạn thảo — heading (tiêu đề), bold (đậm), italic (nghiêng),
        // link (chèn link), bulletedList (danh sách không thứ tự), numberedList (danh sách có thứ tự),
        // outdent/indent (lùi vào/ra), imageUpload (upload ảnh), blockQuote (trích dẫn),
        // insertTable (chèn bảng), mediaEmbed (nhúng media), undo/redo (hoàn tác/làm lại)
        toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', '|',
                  'outdent', 'indent', '|', 'imageUpload', 'blockQuote', 'insertTable', 'mediaEmbed',
                  'undo', 'redo']
    })
    .catch(error => console.error(error)); // Bắt lỗi nếu khởi tạo thất bại
</script>
