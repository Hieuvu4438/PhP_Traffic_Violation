<?php
namespace App\Models;

use App\Core\Model;

class TrafficSign extends Model
{
    protected string $table = 'traffic_signs';

    public function getWithGroup(): array
    {
        $sql = "SELECT s.*, g.name as group_name, g.sign_prefix
                FROM traffic_signs s
                LEFT JOIN traffic_sign_groups g ON s.group_id = g.id
                ORDER BY g.sort_order, s.sign_code";
        return $this->db->query($sql)->fetchAll();
    }

    public function findByGroup(int $groupId): array
    {
        return $this->findAllBy('group_id', $groupId, 'sign_code ASC');
    }

    public function search(string $keyword): array
    {
        $sql = "SELECT s.*, g.name as group_name
                FROM traffic_signs s
                LEFT JOIN traffic_sign_groups g ON s.group_id = g.id
                WHERE s.name LIKE :kw OR s.sign_code LIKE :kw2
                ORDER BY s.sign_code";
        $stmt = $this->db->prepare($sql);
        $like = "%{$keyword}%";
        $stmt->execute(['kw' => $like, 'kw2' => $like]);
        return $stmt->fetchAll();
    }
}
