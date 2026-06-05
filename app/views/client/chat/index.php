<?php
use App\Core\Session;

require __DIR__ . '/../../partials/alerts.php';
$isLoggedIn = Session::isLoggedIn();
$conversationId = $conversation['id'] ?? 0;
$guestToken = $conversation['guest_token'] ?? '';
?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h4 mb-1"><i class="fas fa-comments me-2"></i>Support Chat</h1>
                            <p class="mb-0 small">Send questions to our support team, admin will respond as soon as possible.</p>
                        </div>
                        <span class="badge bg-light text-primary" id="chatStatus">Open</span>
                    </div>

                    <div class="card-body">
                        <?php if (!$isLoggedIn && !$conversationId): ?>
                        <form id="chatStartForm" class="mb-4">
                            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="chatName">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="chatName" name="name" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="chatEmail">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="chatEmail" name="email" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="chatPhone">Phone Number</label>
                                    <input type="tel" class="form-control" id="chatPhone" name="phone" placeholder="e.g. 0912345678">
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-comment-dots me-2"></i>Start Chat
                                    </button>
                                </div>
                            </div>
                        </form>
                        <?php endif; ?>

                        <div id="chatBox" class="border rounded bg-white p-3 mb-3" style="height: 420px; overflow-y: auto; <?= (!$isLoggedIn && !$conversationId) ? 'display:none;' : '' ?>">
                            <div class="text-center text-muted py-5" id="chatEmpty">No messages yet.</div>
                        </div>

                        <form id="chatSendForm" style="<?= (!$isLoggedIn && !$conversationId) ? 'display:none;' : '' ?>">
                            <input type="hidden" name="csrf_token" value="<?= Session::csrfToken() ?>">
                            <div class="input-group">
                                <textarea class="form-control" name="message" rows="2" placeholder="Type your message..." required></textarea>
                                <button class="btn btn-primary" type="submit">
                                    <i class="fas fa-paper-plane me-1"></i>Send
                                </button>
                            </div>
                            <div class="form-text">Maximum 2000 characters per message.</div>
                        </form>

                        <div class="alert alert-danger mt-3 d-none" id="chatError"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
window.chatConfig = {
    conversationId: <?= (int) $conversationId ?>,
    isLoggedIn: <?= $isLoggedIn ? 'true' : 'false' ?>,
    guestToken: '<?= htmlspecialchars($guestToken, ENT_QUOTES, 'UTF-8') ?>',
    startUrl: '/chat/start',
    restoreUrl: '/chat/restore',
    csrfToken: '<?= htmlspecialchars(Session::csrfToken(), ENT_QUOTES, 'UTF-8') ?>'
};
</script>
<script src="/assets/js/chat-client.js"></script>
