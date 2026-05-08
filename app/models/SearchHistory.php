<?php
namespace App\Models;

use App\Core\Model;

class SearchHistory extends Model
{
    protected string $table = 'search_history';

    public function getByUser(int $userId, int $limit = 20): array
    {
        return $this->all(['user_id' => $userId], 'searched_at DESC', $limit);
    }

    public function log(?int $userId, string $plateNumber, string $vehicleType, int $resultCount): void
    {
        $this->create([
            'user_id' => $userId,
            'plate_number' => $plateNumber,
            'vehicle_type' => $vehicleType,
            'result_count' => $resultCount,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'searched_at' => date('Y-m-d H:i:s'),
        ]);
    }
}
