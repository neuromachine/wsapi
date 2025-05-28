<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public function items()
    {
        return $this->hasMany(BlockItem::class);
    }
}
