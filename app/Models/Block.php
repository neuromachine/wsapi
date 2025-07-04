<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public function properties()
    {
        return $this->hasMany(BlockItemProperty::class, 'block_id');
    }

    public function items()
    {
        return $this->hasMany(BlockItem::class, 'block_id')->with('properties.property');
    }

    public function itemsForCategory($categoryId)
    {
        return $this->hasMany(BlockItem::class, 'block_id')->with('properties.property')->where('category_id', $categoryId);;
    }
}
