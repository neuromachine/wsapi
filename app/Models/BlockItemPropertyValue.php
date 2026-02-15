<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockItemPropertyValue extends Model
{

    protected $fillable = [
        'property_id',
        'item_id',
        'value',
        'value_type',
        'locale',
        'version'
    ];

    public function property()
    {
        return $this->belongsTo(BlockItemProperty::class, 'property_id');
    }

    // Отношения (уже должны быть)
    public function item()
    {
        return $this->belongsTo(BlockItem::class, 'item_id');
    }


}
