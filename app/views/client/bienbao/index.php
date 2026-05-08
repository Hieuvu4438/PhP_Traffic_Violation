<?php use App\Core\Session; ?>

<!--
  Trang danh sách biển báo giao thông (client/bienbao/index.php)
  - Form tìm kiếm: ô input search + nút Tìm/Xóa
  - Bộ lọc theo nhóm biển báo: các nút filter (Tất cả + từng nhóm)
  - Danh sách biển báo nhóm theo nhóm, mỗi nhóm có tiêu đề + badge đếm số lượng
  - Mỗi biển báo: card có ảnh (hoặc icon mặc định), mã biển, tên (cắt 60 ký tự)
  - Hiệu ứng hover: translateY(-4px) + đổ bóng đậm hơn
  - $signsByGroup: array nhóm theo tên nhóm -> danh sách biển báo trong nhóm đó
-->

<div class="container py-4">
    <h2 class="fw-bold mb-4">
        <!-- fa-sign: icon biển báo giao thông -->
        <i class="fas fa-sign me-2 text-primary"></i>Tra cứu biển báo giao thông
    </h2>

    <!-- Partial hiển thị thông báo -->
    <?php require __DIR__ . '/../../partials/alerts.php'; ?>

    <!-- === Thanh tìm kiếm & bộ lọc === -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <!-- row g-3: lưới với gutter gap 1rem -->
            <div class="row g-3">
                <!-- Form tìm kiếm text: col-md-6 -->
                <div class="col-md-6">
                    <!-- method="GET" để query string: /bien-bao?q=tu-khoa -->
                    <form method="GET" action="/bien-bao" class="input-group">
                        <!-- input-group-text: vùng icon trong input group -->
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <!-- Giữ lại từ khóa đã nhập trong input -->
                        <input type="text" class="form-control" name="q"
                               placeholder="Tìm kiếm biển báo (tên, mã...)"
                               value="<?= htmlspecialchars($keyword ?? '', ENT_QUOTES, 'UTF-8') ?>">
                        <!-- Nút Tìm -->
                        <button type="submit" class="btn btn-primary">Tìm</button>
                        <!-- Nếu có từ khóa thì hiển thị nút "Xóa" (reset filter) -->
                        <?php if (!empty($keyword)): ?>
                            <a href="/bien-bao" class="btn btn-outline-secondary">Xóa</a>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Bộ lọc theo nhóm biển báo: col-md-6 -->
                <div class="col-md-6">
                    <!-- d-flex flex-wrap gap-2: flexbox với các nút gói xuống dòng, gap 0.5rem -->
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Nút "Tất cả": active (btn-primary) khi không có group được chọn -->
                        <a href="/bien-bao" class="btn <?= empty($currentGroup) ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                            Tất cả
                        </a>
                        <!-- Lặp qua tất cả nhóm biển báo, đánh dấu active nếu đang lọc -->
                        <?php foreach ($groups as $group): ?>
                            <a href="/bien-bao?group=<?= htmlspecialchars($group['id'], ENT_QUOTES, 'UTF-8') ?>"
                               class="btn <?= ($currentGroup ?? '') === (string) $group['id'] ? 'btn-primary' : 'btn-outline-primary' ?> btn-sm">
                                <?= htmlspecialchars($group['name'], ENT_QUOTES, 'UTF-8') ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- === Danh sách biển báo === -->
    <?php if (empty($signsByGroup)): ?>
        <!-- Trạng thái rỗng: không tìm thấy biển báo nào -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-3"></i>
            <h4 class="text-muted">Không tìm thấy biển báo nào</h4>
            <p class="text-muted">Thử tìm kiếm với từ khóa khác.</p>
            <a href="/bien-bao" class="btn btn-outline-primary">Xem tất cả</a>
        </div>
    <?php else: ?>
        <!-- Duyệt qua từng nhóm biển báo: $groupName = tên nhóm, $signs = danh sách biển -->
        <?php foreach ($signsByGroup as $groupName => $signs): ?>
            <!-- Nếu có tên nhóm (không rỗng): hiển thị tiêu đề nhóm -->
            <?php if (!empty($groupName)): ?>
                <div class="mb-4">
                    <!-- border-bottom pb-2: đường viền dưới + padding bottom 0.5rem -->
                    <h4 class="fw-bold border-bottom pb-2 mb-3 text-primary">
                        <!-- fa-folder: icon thư mục (phân nhóm) -->
                        <i class="fas fa-folder me-2"></i><?= htmlspecialchars($groupName, ENT_QUOTES, 'UTF-8') ?>
                        <!-- badge bg-secondary: huy hiệu xám đếm số lượng biển báo trong nhóm -->
                        <span class="badge bg-secondary ms-2"><?= count($signs) ?></span>
                    </h4>
            <?php endif; ?>

            <!-- Lưới biển báo 4 cột desktop, 2 cột mobile -->
            <!-- col-md-4 col-lg-3 col-6: 3/12 tablet, 3/12 desktop, 6/12 mobile -->
            <div class="row g-3 mb-4">
                <?php foreach ($signs as $sign): ?>
                    <div class="col-md-4 col-lg-3 col-6">
                        <!-- Link đến trang chi tiết biển báo -->
                        <a href="/bien-bao/<?= htmlspecialchars($sign['id'], ENT_QUOTES, 'UTF-8') ?>"
                           class="text-decoration-none">
                            <!-- sign-card: class tùy chỉnh cho hiệu ứng hover (CSS ở dưới) -->
                            <div class="card shadow-sm h-100 border-0 sign-card">
                                <!-- Ảnh biển báo (nếu có) -->
                                <?php if (!empty($sign['image'])): ?>
                                    <!-- object-fit: contain: ảnh vừa khung, không cắt -->
                                    <img src="/<?= htmlspecialchars($sign['image'], ENT_QUOTES, 'UTF-8') ?>"
                                         class="card-img-top p-3" alt="<?= htmlspecialchars($sign['name'], ENT_QUOTES, 'UTF-8') ?>"
                                         style="height: 140px; object-fit: contain;">
                                <?php else: ?>
                                    <!-- Icon mặc định khi không có ảnh -->
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center p-3"
                                         style="height: 140px;">
                                        <i class="fas fa-sign fa-3x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                <!-- Thông tin biển báo: mã + tên (cắt 60 ký tự nếu quá dài) -->
                                <div class="card-body text-center p-2">
                                    <!-- badge bg-secondary: huy hiệu mã biển báo (VD: P.102) -->
                                    <span class="badge bg-secondary mb-1"><?= htmlspecialchars($sign['sign_code'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <!-- mb_strlen > 60: cắt tên nếu dài hơn 60 ký tự, thêm dấu ... -->
                                    <p class="card-text small mb-0 text-dark">
                                        <?= htmlspecialchars(mb_strlen($sign['name']) > 60 ? mb_substr($sign['name'], 0, 60) . '...' : $sign['name'], ENT_QUOTES, 'UTF-8') ?>
                                    </p>
                                </div>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Đóng thẻ div mở từ tiêu đề nhóm -->
            <?php if (!empty($groupName)): ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- CSS tùy chỉnh cho hiệu ứng hover trên card biển báo -->
<style>
.sign-card {
    /* transition: thời gian chuyển động 0.2s cho transform và box-shadow */
    transition: transform 0.2s, box-shadow 0.2s;
}
.sign-card:hover {
    /* translateY(-4px): đẩy card lên trên 4px khi hover */
    transform: translateY(-4px);
    /* box-shadow: đổ bóng đậm hơn khi hover */
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>
