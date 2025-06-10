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

//    //TODO: Рефакторинг children и childrenRecursive
//    public function childrenRecursive()
//    {
//        return $this->hasMany(self::class, 'parent_id')
//            ->with('childrenRecursive');
//    }


    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

//    public function blocks() {
//        return $this->hasMany(Block::class, 'category_id');
//    }

    public function blocks()
    {
        return $this->hasManyThrough(
            Block::class,        // конечная модель
            BlockItem::class,     // промежуточная
            'category_id',        // FK в block_items → blocks_categories.id
            'id',                 // PK в blocks → связан через block_items.block_id
            'id',                 // PK этой модели (blocks_categories.id)
            'block_id'            // FK в block_items → blocks.id
        )->distinct();
    }

}
