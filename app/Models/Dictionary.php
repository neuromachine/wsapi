<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dictionary extends Model
{
    public function Dictionaryitems()
    {
        return $this->hasMany(\App\Models\DictionaryItem::class);
    }

    public function DictionaryProperties()
    {
        return $this->hasMany(\App\Models\DictionaryProperty::class, 'dictionary_id', 'id');
    }

    public function RootDictionary()
    {
        return $this->BelongsTo(\App\Models\DictionaryItemCategory::class, 'root_category_id', 'id');
    }

    public function rootCategory()
    {
        return $this->BelongsTo(\App\Models\DictionaryItemCategory::class, 'root_category_id', 'id');
    }

    public function categories()
    {
        return $this->hasMany(DictionaryItemCategory::class, 'dictionary_id', 'id')
            ->where('dictionary_id', 'id')
            ->with('childrenRecursive'); // рекурсивно вложенные
    }

}

