<?php
namespace App\Models;

use App\Core\Model;

class Location extends Model
{
    protected string $table = 'locations';

    public function findByType(string $type): array
    {
        return $this->findAllBy('type', $type, 'name ASC');
    }

    public function getActiveLocations(): array
    {
        return $this->all(['status' => 1], 'type, name ASC');
    }
}
