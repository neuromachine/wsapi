<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockItem extends Model
{
    public function category()
    {
        return $this->belongsTo(BlocksCategories::class, 'category_id');
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

}
