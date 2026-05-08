<?php
namespace App\Models;

use App\Core\Model;

class TrafficAlert extends Model
{
    protected string $table = 'traffic_alerts';

    public function getActive(): array
    {
        $sql = "SELECT * FROM traffic_alerts
                WHERE status = 1
                AND (expires_at IS NULL OR expires_at > NOW())
                ORDER BY created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }
}
