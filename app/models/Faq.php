<?php
namespace App\Models;

use App\Core\Model;

class Faq extends Model
{
    protected string $table = 'faqs';

    public function getActive(): array
    {
        return $this->all(['status' => 1], 'sort_order ASC');
    }
}
