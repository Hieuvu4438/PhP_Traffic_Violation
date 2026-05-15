<?php
use App\Core\Helper;

require __DIR__ . '/../../partials/alerts.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">Chat khách hàng</h2>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Email</th>
                    <th>Trạng thái</th>
                    <th>Chưa đọc</th>
                    <th>Cập nhật</th>
                    <th class="text-end">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($items)): ?>
                    <tr><td colspan="7" class="text-center text-muted py-3">Chưa có cuộc trò chuyện nào.</td></tr>
                <?php else: foreach ($items as $item): ?>
                <?php
                $name = $item['user_name'] ?: ($item['guest_name'] ?: 'Khách');
                $email = $item['user_email'] ?: ($item['guest_email'] ?: '—');
                $unread = (int) ($item['unread_count'] ?? 0);
                ?>
                <tr class="<?= $unread > 0 ? 'fw-bold' : '' ?>">
                    <td><?= (int) $item['id'] ?></td>
                    <td><?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?></td>
                    <td>
                        <span class="badge <?= $item['status'] === 'open' ? 'bg-success' : 'bg-secondary' ?>">
                            <?= $item['status'] === 'open' ? 'Đang mở' : 'Đã đóng' ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($unread > 0): ?>
                            <span class="badge bg-danger"><?= $unread ?></span>
                        <?php else: ?>
                            <span class="text-muted">0</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars(Helper::formatDateTime($item['last_message_at'] ?: $item['created_at']), ENT_QUOTES, 'UTF-8') ?></td>
                    <td class="text-end">
                        <a href="/admin/chat/<?= (int) $item['id'] ?>" class="btn btn-sm btn-primary">
                            <i class="fas fa-comments me-1"></i>Mở chat
                        </a>
                    </td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$pagination = $data;
require __DIR__ . '/../../partials/pagination.php';
?>
