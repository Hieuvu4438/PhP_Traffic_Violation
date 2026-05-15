<?php
namespace App\Models;

use App\Core\Model;

class ChatConversation extends Model
{
    protected string $table = 'chat_conversations';

    public function findOpenByUser(int $userId): ?array
    {
        $stmt = $this->query(
            "SELECT * FROM {$this->table} WHERE user_id = :user_id AND status = 'open' ORDER BY last_message_at DESC, created_at DESC LIMIT 1",
            ['user_id' => $userId]
        );
        $conversation = $stmt->fetch();
        return $conversation ?: null;
    }

    public function findOpenByGuestToken(string $token): ?array
    {
        $stmt = $this->query(
            "SELECT * FROM {$this->table} WHERE guest_token = :guest_token AND status = 'open' LIMIT 1",
            ['guest_token' => $token]
        );
        $conversation = $stmt->fetch();
        return $conversation ?: null;
    }

    public function touchLastMessage(int $id): bool
    {
        return $this->update($id, ['last_message_at' => date('Y-m-d H:i:s')]);
    }

    public function close(int $id): bool
    {
        return $this->update($id, ['status' => 'closed']);
    }

    public function paginateWithUnreadCount(int $page = 1, int $perPage = 10): array
    {
        $total = $this->count();
        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $totalPages));
        $offset = ($page - 1) * $perPage;

        $stmt = $this->query(
            "SELECT c.*, u.fullname AS user_name, u.email AS user_email, COALESCE(unread.unread_count, 0) AS unread_count
             FROM {$this->table} c
             LEFT JOIN users u ON u.id = c.user_id
             LEFT JOIN (
                 SELECT conversation_id, COUNT(*) AS unread_count
                 FROM chat_messages
                 WHERE sender_type = 'user' AND is_read = 0
                 GROUP BY conversation_id
             ) unread ON unread.conversation_id = c.id
             ORDER BY COALESCE(c.last_message_at, c.created_at) DESC
             LIMIT {$perPage} OFFSET {$offset}"
        );

        return [
            'items' => $stmt->fetchAll(),
            'total' => $total,
            'page' => $page,
            'per_page' => $perPage,
            'total_pages' => $totalPages,
            'has_next' => $page < $totalPages,
            'has_prev' => $page > 1,
        ];
    }
}
