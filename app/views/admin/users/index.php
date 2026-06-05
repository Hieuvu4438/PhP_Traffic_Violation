<?php
// ==================================================================================
// VIEW: Quản lý người dùng (Admin) - Danh sách
// Hiển thị bảng danh sách toàn bộ người dùng, kèm phân trang.
// Mỗi dòng có nút: Sửa (btn-warning), Khóa/Mở khóa (toggle-status), Xóa (btn-danger).
// Dữ liệu truyền vào: $items (danh sách user), $data (thông tin phân trang).
// ==================================================================================

// Nhúng partial hiển thị thông báo flash (thành công / lỗi) từ session
require __DIR__ . '/../../partials/alerts.php';
?>

<!-- ========== Hàng tiêu đề + nút Thêm ========== -->
<!-- d-flex...align-items-center: flexbox, justify-content-between: căn 2 bên trái-phải, align-items-center: căn dọc giữa, mb-3: margin-bottom 1rem -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <!-- mb-0: không margin-bottom (căn chỉnh với nút bên phải) -->
    <h2 class="mb-0">Manage Users</h2>
    <!-- btn btn-primary: nút màu xanh dương; fas fa-plus me-1: icon dấu cộng + margin-right 0.25rem -->
    <a href="/admin/users/create" class="btn btn-primary"><i class="fas fa-plus me-1"></i>Add User</a>
</div>

<!-- ========== Bảng danh sách người dùng ========== -->
<div class="card">
    <!-- card-body p-0: nội dung thẻ, padding = 0 để bảng full-width trong thẻ -->
    <div class="card-body p-0">
        <!-- table table-hover mb-0: bảng có hiệu ứng hover dòng, không margin-bottom -->
        <table class="table table-hover mb-0">
            <!-- thead table-light: phần đầu bảng nền xám nhạt -->
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created</th>
                    <!-- text-end: căn phải nội dung trong ô -->
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <!-- colspan 8: gộp 8 cột; text-center: căn giữa; text-muted: chữ xám; py-3: padding trên/dưới 1rem -->
                    <tr><td colspan="8" class="text-center text-muted py-3">No users found.</td></tr>
                <?php else:
                    // Lặp qua danh sách người dùng
                    foreach ($items as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <!-- htmlspecialchars() chống XSS cho tên người dùng -->
                    <td><?= htmlspecialchars($user['fullname'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- SĐT: nếu null thì hiển thị dấu gạch ngang -->
                    <td><?= htmlspecialchars($user['phone'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <!-- Hiển thị vai trò dạng badge: admin => nền đỏ (bg-danger), user => nền xanh dương (bg-primary) -->
                        <span class="badge <?= $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary' ?>">
                            <?= $user['role'] === 'admin' ? 'Admin' : 'User' ?>
                        </span>
                    </td>
                    <td>
                        <!-- Hiển thị trạng thái dạng badge: status=1 (hoạt động) => xanh lá (bg-success), status=0 (bị khóa) => đỏ (bg-danger) -->
                        <span class="badge <?= $user['status'] ? 'bg-success' : 'bg-danger' ?>">
                            <?= $user['status'] ? 'Active' : 'Locked' ?>
                        </span>
                    </td>
                    <!-- Định dạng ngày tạo bằng Helper::formatDate() -->
                    <td><?= htmlspecialchars(\App\Core\Helper::formatDate($user['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <!-- Cột thao tác: các nút Sửa / Khóa-Mở khóa / Xóa, căn phải -->
                    <td class="text-end">
                        <!-- Nút Sửa: btn-sm = nút nhỏ, btn-warning = nền vàng, title="Sửa" = tooltip, fa-edit = icon bút sửa -->
                        <a href="/admin/users/<?= $user['id'] ?>/edit" class="btn btn-sm btn-warning" title="Edit"><i class="fas fa-edit"></i></a>
                        <!-- Nút toggle trạng thái (Khóa/Mở khóa) gửi AJAX:
                             - Nếu đang hoạt động (status=1): nút xám (btn-secondary), icon fa-lock (ổ khóa)
                             - Nếu bị khóa (status=0): nút xanh (btn-success), icon fa-unlock (mở khóa)
                             - data-id, data-type, data-current-status: dữ liệu gửi qua AJAX để JS xử lý -->
                        <button type="button" class="btn btn-sm <?= $user['status'] ? 'btn-secondary' : 'btn-success' ?> toggle-status-btn"
                                data-id="<?= $user['id'] ?>"
                                data-type="user"
                                data-current-status="<?= $user['status'] ?>"
                                title="<?= $user['status'] ? 'Lock' : 'Unlock' ?>">
                            <i class="fas <?= $user['status'] ? 'fa-lock' : 'fa-unlock' ?>"></i>
                        </button>
                        <!-- Form xóa người dùng: POST method, class delete-form để JS bắt sự kiện confirm trước khi xóa -->
                        <form method="POST" action="/admin/users/<?= $user['id'] ?>/delete" class="delete-form d-inline">
                            <!-- CSRF token chống tấn công Cross-Site Request Forgery -->
                            <input type="hidden" name="csrf_token" value="<?= \App\Core\Session::csrfToken() ?>">
                            <!-- btn-danger: nền đỏ, fa-trash: icon thùng rác -->
                            <button type="submit" class="btn btn-sm btn-danger" title="Delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
// Nhúng partial phân trang (pagination.php), truyền $data chứa $data['currentPage'], $data['totalPages'], etc.
// $pagination được gán từ $data để partial sử dụng
$pagination = $data; require __DIR__ . '/../../partials/pagination.php';
?>
