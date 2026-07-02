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

        return BlocksCategories::with($this->categoryRelations($locale, $category))
            ->where('id', $category->id)
            ->first();
    }

    private function categoryRelations(string $locale, BlocksCategories $category): array
    {
        return [
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

            'children' => function ($q) use ($locale) {
                $q->whereHas('items', function ($sub) use ($locale) {
                    $sub->whereHas('propertyValues', function ($deep) use ($locale) {
                        $deep->where('locale', $locale);
                    });
                });
            },
            'children.items' => function ($q) use ($locale) {
                $q->whereHas('propertyValues', function ($sub) use ($locale) {
                    $sub->where('locale', $locale);
                });
            },
            'children.items.propertyValues' => function ($q) use ($locale) {
                $q->where('locale', $locale);
            },
            'children.items.propertyValues.property',
        ];
    }

    public function getOffersData(string $locale, string $slug): array
    {
        $category = BlocksCategories::where('key', $slug)->firstOrFail();
        $block = \App\Models\Block::where('key', 'offers')->firstOrFail();

        $items = $block->items()
            ->where('category_id', $category->id)
            ->with(['propertyValues.property']) // подгружаем связи
            ->get();

        return [
            'category' => $category,
            'block' => $block,
            'items' => $items,
        ];
    }
}
