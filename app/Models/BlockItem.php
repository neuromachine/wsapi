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
        return $this->belongsTo(Block::class, 'block_id');
    }

    public function properties()
    {
        return $this->hasMany(BlockItemPropertyValue::class, 'item_id')
            ->with('property');
    }

    // TODO: а где метод для ->with('property')?
    public function propertyValues()
    {
        return $this->hasMany(BlockItemPropertyValue::class, 'item_id')
            ->with('property');
    }

}
