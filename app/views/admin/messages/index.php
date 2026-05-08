<?php
// ==================================================================================
// VIEW: Quản lý tin nhắn liên hệ (Admin) - Danh sách
// Hiển thị bảng danh sách tin nhắn từ form liên hệ của người dùng.
// Tin chưa đọc: in đậm (fw-bold), badge đỏ (bg-danger: Chưa đọc).
// Tin đã đọc: chữ thường, badge xanh (bg-success: Đã đọc).
// Có nút: Xem (modal chi tiết), Đánh dấu đã đọc, Xóa.
// Dữ liệu: $items (danh sách tin nhắn), $data (thông tin phân trang).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Hàng tiêu đề ========== -->
<!-- d-flex...align-items-center: flexbox căn đều 2 bên, căn dọc giữa; mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Quản lý tin nhắn liên hệ</h2>
    <!-- Không có nút Thêm vì tin nhắn do người dùng gửi từ form liên hệ, admin chỉ xem và quản lý -->
</div>

<!-- ========== Bảng danh sách tin nhắn ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ không padding -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng hover dòng, không margin-bottom -->
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Người gửi</th>
                    <th>Email</th>
                    <th>Tiêu đề</th>
                    <th>Trạng thái</th>
                    <th>Ngày gửi</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <!-- colspan 7: gộp 7 cột; text-center text-muted py-3: căn giữa, chữ xám, padding trên/dưới 1rem -->
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có tin nhắn nào.</td></tr>
                <?php else:
                    foreach ($items as $msg): ?>
                <!-- fw-bold: in đậm toàn bộ dòng nếu tin nhắn chưa đọc, giúp admin dễ phân biệt -->
                <tr class="<?= !$msg['is_read'] ? 'fw-bold' : '' ?>">
                    <td><?= $msg['id'] ?></td>
                    <td><?= htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Cắt tiêu đề còn 50 ký tự nếu dài -->
                    <td><?= htmlspecialchars(\App\Core\Helper::truncate($msg['subject'] ?? '—', 50), ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <!-- Badge trạng thái đọc:
                             is_read=1 => bg-success (xanh lá): Đã đọc
                             is_read=0 => bg-danger (đỏ): Chưa đọc -->
                        <span class="badge <?= $msg['is_read'] ? 'bg-success' : 'bg-danger' ?>">
                            <?= $msg['is_read'] ? 'Đã đọc' : 'Chưa đọc' ?>
                        </span>
                    </td>
                    <!-- Định dạng ngày gửi -->
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($msg['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <!-- Nút Xem chi tiết: btn-sm btn-info text-white = nút nhỏ xanh cyan chữ trắng -->
                        <!-- data-bs-toggle="modal" data-bs-target="#viewMsg{id}": mở modal xem chi tiết tin nhắn -->
                        <!-- fa-eye: icon con mắt (xem) -->
                        <button class="btn btn-sm btn-info text-white" data-bs-toggle="modal" data-bs-target="#viewMsg<?= $msg['id'] ?>" title="Xem"><i class="fas fa-eye"></i></button>
                        <?php if (!$msg['is_read']): ?>
                        <!-- Nút Đánh dấu đã đọc: chỉ hiển thị khi tin nhắn chưa đọc -->
                        <!-- btn-sm btn-success: nút nhỏ xanh lá; fa-check: icon dấu tích -->
                        <form method="POST" action="/admin/messages/<?= $msg['id'] ?>/read" class="d-inline">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <button type="submit" class="btn btn-sm btn-success" title="Đánh dấu đã đọc"><i class="fas fa-check"></i></button>
                        </form>
                        <?php endif; ?>
                        <!-- Form xóa tin nhắn: class="delete-form d-inline" để JS confirm trước khi submit -->
                        <form method="POST" action="/admin/messages/<?= $msg['id'] ?>/delete" class="delete-form d-inline">
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <!-- btn-sm btn-danger: nút nhỏ đỏ; fa-trash: icon thùng rác -->
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

<!-- ==================== MODAL CHI TIẾT TIN NHẮN ==================== -->
<!-- Tạo 1 modal cho mỗi tin nhắn (lặp qua $items), id động: viewMsg{id} -->
<?php foreach ($items as $msg): ?>
<!-- modal fade: hộp thoại popup có hiệu ứng mờ dần + trượt; tabindex="-1": không focus bằng Tab -->
<div class="modal fade" id="viewMsg<?= $msg['id'] ?>" tabindex="-1">
    <!-- modal-dialog: khung hộp thoại căn giữa -->
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- modal-header: phần đầu modal -->
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết tin nhắn #<?= $msg['id'] ?></h5>
                <!-- btn-close: nút X đóng modal; data-bs-dismiss="modal": đóng modal khi click -->
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- modal-body: nội dung chính modal — hiển thị đầy đủ thông tin tin nhắn -->
            <div class="modal-body">
                <p><strong>Người gửi:</strong> <?= htmlspecialchars($msg['name'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($msg['email'], ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Tiêu đề:</strong> <?= htmlspecialchars($msg['subject'] ?? 'Không có', ENT_QUOTES, 'UTF-8') ?></p>
                <p><strong>Ngày gửi:</strong> <?= htmlspecialchars(\App\Core\Helper::formatDateTime($msg['created_at']), ENT_QUOTES, 'UTF-8') ?></p>
                <!-- hr: đường kẻ ngang phân cách -->
                <hr>
                <p><strong>Nội dung:</strong></p>
                <!-- border rounded p-3 bg-light: viền, bo góc, padding 1rem, nền xám nhạt -->
                <!-- nl2br(): chuyển ký tự xuống dòng (\n) thành thẻ <br> để hiển thị đúng định dạng -->
                <div class="border rounded p-3 bg-light"><?= nl2br(htmlspecialchars($msg['message'], ENT_QUOTES, 'UTF-8')) ?></div>
            </div>

            <!-- modal-footer: phần chân modal -->
            <div class="modal-footer">
                <!-- btn btn-secondary: nút xám Đóng -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <?php if (!$msg['is_read']): ?>
                <!-- Nếu tin chưa đọc: hiển thị nút Đánh dấu đã đọc trong modal -->
                <!-- btn btn-success: nút xanh lá -->
                <form method="POST" action="/admin/messages/<?= $msg['id'] ?>/read">
                    <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                    <button type="submit" class="btn btn-success">Đánh dấu đã đọc</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
