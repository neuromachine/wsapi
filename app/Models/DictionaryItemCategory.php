<?php

namespace App\Models;

use App\Traits\HasParent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class DictionaryItemCategory extends Model
{
    use HasParent; // TODO : Legacy -> Refactor

    public function parent()
    {
        return $this->belongsTo(__CLASS__);
    }

    public function children()
    {
        return $this->hasMany(__CLASS__, 'parent_id', 'id')->select('id', 'key', 'parent_id', 'name', 'description', 'content');
    }

    public function DictionaryItems()
    {
        return $this->BelongsToMany(
            \App\Models\DictionaryItem::class,
            'dictionary_items_dictionary_item_categories',
            'dictionary_item_categories_id',
            'dictionary_items_id'

        );
    }

    public function childrenRecursive()
    {
        //return $this->hasMany(__CLASS__, 'parent_id', 'id')->with('childrenRecursive');
        return $this->hasMany(self::class, 'parent_id')->with('childrenRecursive');
    }
}
