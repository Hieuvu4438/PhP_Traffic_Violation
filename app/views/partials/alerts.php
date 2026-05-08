<?php
/**
 * Partial Alerts - Hiển thị thông báo flash (thành công, lỗi, cảnh báo).
 * Dùng session flash: thông báo chỉ hiển thị 1 lần rồi tự xóa khỏi session.
 * Controller set flash trước khi redirect, view này hiển thị thông báo.
 * Được nhúng vào đầu các trang cần hiển thị thông báo.
 */
/* Import Session để lấy flash messages */
use App\Core\Session;
/* getFlash('success'): lấy thông báo thành công (màu xanh) từ session, xóa sau khi đọc */
$success = Session::getFlash('success');
/* getFlash('error'): lấy thông báo lỗi (màu đỏ) từ session, xóa sau khi đọc */
$error = Session::getFlash('error');
/* getFlash('warning'): lấy thông báo cảnh báo (màu vàng) từ session, xóa sau khi đọc */
$warning = Session::getFlash('warning');
?>
<!-- Chỉ hiển thị thông báo nếu có dữ liệu -->
<?php if ($success): ?>
    <!-- alert: class cơ bản của Bootstrap cho thông báo.
         alert-success: nền xanh lá (thành công).
         alert-dismissible: cho phép đóng thông báo bằng nút X.
         fade show: hiệu ứng fade-in khi xuất hiện, cần cho alert-dismissible hoạt động.
         role="alert": thuộc tính ARIA cho trình đọc màn hình (accessibility). -->
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <!-- fa-check-circle: icon dấu tick tròn (thành công). me-2: margin-right 0.5rem -->
        <i class="fas fa-check-circle me-2"></i>
        <!-- htmlspecialchars: chống XSS, escape nội dung thông báo từ server -->
        <?= htmlspecialchars($success) ?>
        <!-- btn-close: nút X đóng thông báo của Bootstrap 5.
             data-bs-dismiss="alert": thuộc tính JS để đóng alert khi click. -->
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <!-- alert-danger: nền đỏ (lỗi/thất bại) -->
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <!-- fa-exclamation-circle: icon dấu chấm than tròn (lỗi) -->
        <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if ($warning): ?>
    <!-- alert-warning: nền vàng (cảnh báo/chú ý) -->
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <!-- fa-exclamation-triangle: icon tam giác cảnh báo -->
        <i class="fas fa-exclamation-triangle me-2"></i><?= htmlspecialchars($warning) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
