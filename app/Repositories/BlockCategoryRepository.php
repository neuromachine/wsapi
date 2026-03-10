<?php
namespace App\Repositories;

use App\Models\BlocksCategories;

class BlockCategoryRepository
{

    public function getCategoriesRecursive(string $locale,string $key)
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
}
