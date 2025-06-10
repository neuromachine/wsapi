<?php
namespace App\Repositories;

use App\Models\BlocksCategories;
use Illuminate\Database\Eloquent\Collection;

class BlockCategoryRepository
{

    public function getCategoriesRecursive(string $key)
    {
        if($key)
        {
            return BlocksCategories::where('key', $key)->with('childrenRecursive')->get();
        }
        return BlocksCategories::with('childrenRecursive')->whereNull('parent_id')->get();
    }


    public function getCategory(string $key)
    {

        return BlocksCategories::with(
                [
                    'childrenRecursive',             // вложенные категории
                    'blocks.properties',
                    'blocks.items.propertyValues.property',
//                    'items.block.properties',        // у каждой позиции её тип + описание полей
//                    'items.propertyValues.property', // сами значения + метаданные поля
                ]
            )
            ->where('key', $key)
            ->firstOrFail();

    }

/*
    public function getAllWithBlockCount(): Collection
    {
        return BlocksCategory::withCount('blocks')
            ->orderBy('name', 'asc')
            ->get();
    }

    public function findActiveCategoryWithBlocks(int $id): ?BlocksCategory
    {
        return BlocksCategory::where('id', $id)
            ->whereHas('blocks', fn($q) => $q->where('is_active', true))
            ->first();
    }*/
}
