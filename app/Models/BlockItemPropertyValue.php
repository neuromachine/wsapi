<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockItemPropertyValue extends Model
{
    public function property()
    {
        return $this->belongsTo(BlockItemProperty::class, 'property_id');
    }
}
