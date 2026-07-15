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
        // Try to find a category by slug
        $category = BlocksCategories::where('key', $slug)->first();
        $singleItem = null;
        
        // If not found, check if the slug is an item key
        if (!$category) {
            $singleItem = \App\Models\BlockItem::where('key', $slug)->firstOrFail();
            $category = $singleItem->category;
        }
        
        // Find which blocks have items in this category
        $blockIds = \App\Models\BlockItem::where('category_id', $category->id)
            ->distinct()
            ->pluck('block_id');
            
        $blocks = \App\Models\Block::whereIn('id', $blockIds)->get();
        
        // The "offers" block is any block not mapped to standard structural sections
        $offerBlocks = $blocks->reject(function ($b) {
            return \App\Support\BlockAttachMap::get($b->key) !== null;
        });
        
        $block = $offerBlocks->first();
        
        // Fallback to strict 'offers' if no semantic offer block is found
        if (!$block) {
            $block = \App\Models\Block::where('key', 'offers')->firstOrFail();
        }

        $query = $block->items()
            ->where('category_id', $category->id)
            ->with(['propertyValues.property']);
            
        if ($singleItem) {
            $query->where('id', $singleItem->id);
        }
        
        $items = $query->get();

        return [
            'category' => $category,
            'block' => $block,
            'items' => $items,
        ];
    }
}
