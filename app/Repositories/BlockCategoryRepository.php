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
            return BlocksCategories::where('key', $key)->with('childrenRecursive')->firstOrFail();
        }
        return BlocksCategories::with('childrenRecursive')->whereNull('parent_id')->firstOrFail();
    }

    public function getCategory(string $locale, string $key)
    {
        $category = BlocksCategories::where('key', $key)->firstOrFail();

        return BlocksCategories::with([
            'blocks.properties',
            'blocks.items' => function ($q) use ($locale, $category) {
                $q->where('category_id', $category->id)
                    ->whereHas('propertyValues', function ($sub) use ($locale) {
                        $sub->where('locale', $locale);
                    });
            },
            'blocks.items.propertyValues' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'blocks.items.propertyValues.property',
        ])
            ->where('id', $category->id)
            ->first();
    }

/*
    public function getCategory(string $key)
    {

        return BlocksCategories::with(
                [
                    'childrenRecursive',             // вложенные категории
                    'blocks.properties',
                    'blocks.items.propertyValues.property',
                    //TODO: оставлено для вариативного использования см. BlockCategoryResource.php (нужно загрузить данные при возможном использовании)
//                    'items.block.properties',        // у каждой позиции её тип + описание полей
//                    'items.propertyValues.property', // сами значения + метаданные поля
                ]
            )
            ->where('key', $key)
            ->firstOrFail();

    }
*/

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
