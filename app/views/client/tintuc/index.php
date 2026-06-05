<?php
use App\Core\Helper;
?>

<!--
  Trang danh sách tin tức (client/tintuc/index.php)
  - Sidebar trái: bộ lọc theo danh mục (list-group)
  - Nội dung chính: lưới tin tức 3 cột (col-lg-4) dạng card
  - Mỗi card: thumbnail, category badge, title, tóm tắt 100 ký tự, ngày đăng, lượt xem
  - Phân trang ở cuối
  - Hiển thị trạng thái rỗng khi chưa có bài viết
-->

<!-- container py-4: căn giữa nội dung + padding top/bottom -->
<div class="container py-4">
    <!-- Tiêu đề trang | fw-bold: in đậm -->
    <h2 class="fw-bold mb-4">
        <!-- fa-newspaper: icon tờ báo (tin tức) -->
        <i class="fas fa-newspaper me-2 text-primary"></i>Traffic News
    </h2>

    <!-- Partial hiển thị thông báo từ session -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <div class="row">
        <!-- === Sidebar trái: Bộ lọc danh mục (col-lg-3: 3/12 trên desktop) === -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm">
                <!-- card-header: header card | bg-primary text-white: nền xanh chữ trắng | fw-bold: in đậm -->
                <div class="card-header bg-primary text-white fw-bold">
                    <!-- fa-list: icon danh sách -->
                    <i class="fas fa-list me-2"></i>Categories
                </div>
                <!-- list-group-flush: danh sách sát viền, không bo góc -->
                <div class="list-group list-group-flush">
                    <!-- Link "Tất cả": active class khi không có category đang chọn -->
                    <a href="/tin-tuc"
                       class="list-group-item list-group-item-action <?= ($currentCategory ?? '') === '' ? 'active' : '' ?>">
                        All
                    </a>
                    <!-- Lặp qua tất cả danh mục, đánh dấu active nếu đang lọc -->
                    <?php foreach ($categories as $cat): ?>
                        <a href="/tin-tuc?cat=<?= htmlspecialchars($cat['id'], ENT_QUOTES, 'UTF-8') ?>"
                           class="list-group-item list-group-item-action <?= ($currentCategory ?? '') === (string) $cat['id'] ? 'active' : '' ?>">
                            <?= htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- === Nội dung chính: Lưới tin tức (col-lg-9: 9/12 trên desktop) === -->
        <div class="col-lg-9">
            <?php if (empty($news)): ?>
                <!-- Trạng thái rỗng: chưa có bài viết nào -->
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                    <h4 class="text-muted">No articles yet</h4>
                    <p class="text-muted">Please check back later.</p>
                </div>
            <?php else: ?>
                <!-- row g-4: lưới grid gap 1.5rem -->
                <div class="row g-4">
                    <!-- Lặp qua từng bài viết | col-md-6 col-lg-4: 3 cột trên desktop, 2 cột tablet -->
                    <?php foreach ($news as $item): ?>
                        <div class="col-md-6 col-lg-4">
                            <!-- card h-100: full chiều cao dòng | border-0: không viền -->
                            <div class="card shadow-sm h-100 border-0">
                                <!-- Thumbnail hoặc icon mặc định -->
                                <?php if (!empty($item['thumbnail'])): ?>
                                    <!-- object-fit: cover: ảnh cắt vừa khung, giữ tỷ lệ -->
                                    <img src="/<?= htmlspecialchars($item['thumbnail'], ENT_QUOTES, 'UTF-8') ?>"
                                         class="card-img-top" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>"
                                         style="height: 180px; object-fit: cover;">
                                <?php else: ?>
                                    <!-- bg-light: nền xám nhạt | d-flex: flexbox để căn giữa icon -->
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"
                                         style="height: 180px;">
                                        <i class="fas fa-newspaper fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <!-- d-flex flex-column: flex column để đẩy footer xuống dưới -->
                                <div class="card-body d-flex flex-column">
                                    <!-- Category badge: hiển thị tên danh mục dạng huy hiệu -->
                                    <!-- align-self-start: badge không giãn full width -->
                                    <?php if (!empty($item['category_name'])): ?>
                                        <span class="badge bg-primary mb-2 align-self-start">
                                            <?= htmlspecialchars($item['category_name'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    <?php endif; ?>
                                    <h5 class="card-title">
                                        <!-- stretched-link: toàn bộ card là link đến chi tiết bài viết -->
                                        <a href="/tin-tuc/<?= htmlspecialchars($item['slug'] ?? 'bai-viet-' . $item['id'], ENT_QUOTES, 'UTF-8') ?>"
                                           class="text-decoration-none text-dark stretched-link">
                                            <?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>
                                        </a>
                                    </h5>
                                    <!-- Tóm tắt nội dung 100 ký tự | flex-grow-1: đẩy footer xuống dưới -->
                                    <p class="card-text text-muted small flex-grow-1">
                                        <?= htmlspecialchars(Helper::truncate(strip_tags($item['content'] ?? ''), 100), ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                    <!-- Footer card: ngày đăng + lượt xem -->
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <!-- far fa-calendar: icon lịch -->
                                            <i class="far fa-calendar me-1"></i>
                                            <?= htmlspecialchars(Helper::formatDate($item['created_at'], 'd/m/Y'), ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                        <small class="text-muted">
                                            <!-- far fa-eye: icon con mắt (lượt xem) -->
                                            <i class="far fa-eye me-1"></i><?= (int)($item['views'] ?? 0) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- === Phân trang === -->
                <?php if (isset($pagination)): ?>
                    <?php
                    // Xây dựng URL gốc cho phân trang, thêm query string cat= nếu đang lọc
                    $baseUrl = '/tin-tuc';
                    if (!empty($currentCategory)) {
                        $baseUrl .= '?cat=' . $currentCategory;
                    }
                    // Nhúng partial phân trang dùng chung (pagination.php)
                    require __DIR__ . '/../../partials/pagination.php';
                    ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>
