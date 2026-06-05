<?php
namespace App\Models;

use App\Core\Model;

class ContactMessage extends Model
{
    protected string $table = 'contact_messages';

    public function getUnread(): array
    {
        return $this->findAllBy('is_read', 0, 'created_at DESC');
    }

    public function markRead(int $id): void
    {
        $this->update($id, ['is_read' => 1]);
    }
}
