<?php
namespace App\Models;

use App\Core\Model;

class Vehicle extends Model
{
    protected string $table = 'vehicles';

    public function findByUser(int $userId): array
    {
        return $this->findAllBy('user_id', $userId, 'created_at DESC');
    }

    public function countByUser(int $userId): int
    {
        return $this->count(['user_id' => $userId]);
    }
}
