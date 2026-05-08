<?php
namespace App\Models;

use App\Core\Model;

class TrafficSignGroup extends Model
{
    protected string $table = 'traffic_sign_groups';

    public function getAllSorted(): array
    {
        return $this->all([], 'sort_order ASC');
    }
}
