<?php
// app/Models/DictionaryCategory.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DictionaryCategory extends Model
{
    protected $fillable = ['dictionary_id', 'parent_id', 'name'];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->with('children');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function dictionary()
    {
        return $this->belongsTo(Dictionary::class);
    }
}
