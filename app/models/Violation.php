<?php
namespace App\Models;

use App\Core\Model;

class Violation extends Model
{
    protected string $table = 'violations';

    /**
     * Tra cứu vi phạm theo biển số và loại xe
     */
    public function searchByPlate(string $plateNumber, string $vehicleType): array
    {
        $sql = "SELECT v.*, l.name as location_name, l.address as location_address,
                       o.name as offense_name, o.penalty as offense_penalty
                FROM violations v
                LEFT JOIN locations l ON v.location_id = l.id
                LEFT JOIN offenses o ON v.offense_id = o.id
                WHERE v.plate_number = :plate AND v.vehicle_type = :type
                ORDER BY v.violation_date DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['plate' => $plateNumber, 'type' => $vehicleType]);
        return $stmt->fetchAll();
    }

    /**
     * Thống kê top lỗi vi phạm
     */
    public function topOffenses(int $limit = 10): array
    {
        $sql = "SELECT o.name as offense_name, COUNT(*) as count
                FROM violations v
                JOIN offenses o ON v.offense_id = o.id
                GROUP BY v.offense_id, o.name
                ORDER BY count DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Thống kê top địa điểm vi phạm
     */
    public function topLocations(int $limit = 10): array
    {
        $sql = "SELECT l.name as location_name, l.address, COUNT(*) as count
                FROM violations v
                JOIN locations l ON v.location_id = l.id
                GROUP BY v.location_id, l.name, l.address
                ORDER BY count DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Thống kê top biển số vi phạm nhiều nhất
     */
    public function topPlates(int $limit = 10): array
    {
        $sql = "SELECT plate_number, vehicle_type, COUNT(*) as count
                FROM violations
                GROUP BY plate_number, vehicle_type
                ORDER BY count DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Thống kê vi phạm theo tháng trong năm
     */
    public function countByMonth(int $year): array
    {
        $sql = "SELECT MONTH(violation_date) as month, COUNT(*) as count
                FROM violations
                WHERE YEAR(violation_date) = :year
                GROUP BY MONTH(violation_date)
                ORDER BY month";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['year' => $year]);
        return $stmt->fetchAll();
    }

    /**
     * Tổng số vi phạm theo trạng thái
     */
    public function countByStatus(): array
    {
        $sql = "SELECT status, COUNT(*) as count FROM violations GROUP BY status";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Tổng số vi phạm hôm nay
     */
    public function countToday(): int
    {
        $sql = "SELECT COUNT(*) FROM violations WHERE DATE(violation_date) = CURDATE()";
        return (int) $this->db->query($sql)->fetchColumn();
    }

    /**
     * Get violation với JOIN
     */
    public function getAllWithDetails(array $conditions = [], string $orderBy = 'v.violation_date DESC', int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT v.*, l.name as location_name, o.name as offense_name
                FROM violations v
                LEFT JOIN locations l ON v.location_id = l.id
                LEFT JOIN offenses o ON v.offense_id = o.id";

        $params = [];
        if (!empty($conditions)) {
            $clauses = [];
            foreach ($conditions as $key => $value) {
                $clauses[] = "v.{$key} = :{$key}";
                $params[$key] = $value;
            }
            $sql .= ' WHERE ' . implode(' AND ', $clauses);
        }

        $sql .= " ORDER BY {$orderBy}";

        if ($limit > 0) {
            $sql .= " LIMIT {$limit}";
            if ($offset > 0) {
                $sql .= " OFFSET {$offset}";
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
