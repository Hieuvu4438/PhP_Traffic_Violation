<?php
use App\Core\Session;
use App\Core\Helper;

require __DIR__ . '/../../partials/alerts.php';
$name = $conversation['guest_name'] ?: 'Khách hàng';
$email = $conversation['guest_email'] ?: '—';
$phone = $conversation['guest_phone'] ?: '—';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h2 class="mb-1">Chi tiết chat #<?= (int) $conversation['id'] ?></h2>
        <div class="text-muted">
            <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?> ·
            <?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?> ·
            <?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="/admin/chat" class="btn btn-outline-secondary">Quay lại</a>
        <?php if ($conversation['status'] === 'open'): ?>
        <form method="POST" action="/admin/chat/<?= (int) $conversation['id'] ?>/close">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <button type="submit" class="btn btn-outline-danger">Đóng chat</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>
            <i class="fas fa-comments me-2"></i>Hội thoại
        </span>
        <span class="badge <?= $conversation['status'] === 'open' ? 'bg-success' : 'bg-secondary' ?>" id="chatStatus">
            <?= $conversation['status'] === 'open' ? 'Đang mở' : 'Đã đóng' ?>
        </span>
    </div>
    <div class="card-body">
        <div id="chatBox" class="border rounded bg-light p-3 mb-3" style="height: 480px; overflow-y: auto;">
            <div class="text-center text-muted py-5" id="chatEmpty">Chưa có tin nhắn nào.</div>
        </div>

        <?php if ($conversation['status'] === 'open'): ?>
        <form id="adminChatReplyForm">
            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
            <div class="input-group">
                <textarea class="form-control" name="message" rows="2" placeholder="Nhập phản hồi cho khách hàng..." required></textarea>
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-paper-plane me-1"></i>Gửi
                </button>
            </div>
        </form>
        <?php else: ?>
            <div class="alert alert-secondary mb-0">Cuộc trò chuyện đã đóng, không thể gửi thêm phản hồi.</div>
        <?php endif; ?>

        <div class="alert alert-danger mt-3 d-none" id="chatError"></div>
    </div>
</div>

<script>
window.adminChatConfig = {
    conversationId: <?= (int) $conversation['id'] ?>,
    csrfToken: '<?= htmlspecialchars(Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>'
};
</script>
<script src="/assets/js/chat-admin.js"></script>
