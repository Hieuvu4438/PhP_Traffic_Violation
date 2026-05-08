<?php
namespace App\Models;

use App\Core\Model;

class Offense extends Model
{
    protected string $table = 'offenses';

    public function getWithCategory(): array
    {
        $sql = "SELECT o.*, c.name as category_name
                FROM offenses o
                LEFT JOIN offense_categories c ON o.category_id = c.id
                ORDER BY c.name, o.name";
        return $this->db->query($sql)->fetchAll();
    }
}
