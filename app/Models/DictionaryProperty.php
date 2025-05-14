<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DictionaryProperty extends Model
{

    public function Dictionary()
    {
        return $this->belongsTo(\App\Models\Dictionary::class,'id','dictionary_id');
    }

    public function DictionaryItems()
    {
        return $this->belongsTo(\App\Models\DictionaryItem::class);
    }

    public function DictionaryItemProperty()
    {
        return $this->hasMany(\App\Models\DictionaryItemProperty::class, 'dictionary_property_id');
    }

    public function items()
    {
        return $this->belongsToMany(DictionaryItem::class, 'dictionary_item_properties')
            ->withPivot('value')
            ->withTimestamps();
    }
}
