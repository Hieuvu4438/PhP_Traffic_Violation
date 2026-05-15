<?php
namespace App\Models;

use App\Core\Model;

class ChatMessage extends Model
{
    protected string $table = 'chat_messages';

    public function getByConversation(int $conversationId, int $afterId = 0): array
    {
        $sql = "SELECT m.*, u.fullname AS sender_name
                FROM {$this->table} m
                LEFT JOIN users u ON u.id = m.sender_id
                WHERE m.conversation_id = :conversation_id";
        $params = ['conversation_id' => $conversationId];

        if ($afterId > 0) {
            $sql .= " AND m.id > :after_id";
            $params['after_id'] = $afterId;
        }

        $sql .= " ORDER BY m.id ASC";
        return $this->query($sql, $params)->fetchAll();
    }

    public function markConversationRead(int $conversationId, string $senderType): bool
    {
        $stmt = $this->query(
            "UPDATE {$this->table} SET is_read = 1 WHERE conversation_id = :conversation_id AND sender_type = :sender_type",
            ['conversation_id' => $conversationId, 'sender_type' => $senderType]
        );
        return $stmt->rowCount() >= 0;
    }
}
