<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DictionaryItemProperty extends Model
{
    public function ItemProperty()
    {
        return $this->belongsTo(\App\Models\DictionaryProperty::class, 'dictionary_property_id', 'id');
    }

    public function Item()
    {
        return $this->belongsTo(\App\Models\DictionaryItem::class, 'dictionary_item_id', 'id');
    }

    public function property()
    {
        return $this->belongsTo(DictionaryProperty::class,'dictionary_property_id', 'id');
    }
}
