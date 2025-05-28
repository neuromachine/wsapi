<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlocksCategories extends Model
{
    public function items()
    {
        return $this->hasMany(BlockItem::class, 'category_id');
    }
}
