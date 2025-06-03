<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlocksCategories extends Model
{
    public function items()
    {
        return $this->hasMany(BlockItem::class, 'category_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    public function blocks() {
        return $this->hasMany(Block::class, 'category_id');
    }
}
