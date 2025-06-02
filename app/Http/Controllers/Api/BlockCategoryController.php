<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlocksCategories;
use App\Models\Block;
use App\Http\Resources\BlockItemResource;
use App\Http\Resources\BlockCategoryStructureResource;

class BlockCategoryController extends Controller
{
    public function offers(string $slug)
    {
        $category = BlocksCategories::where('key', $slug)->firstOrFail();
        $block = Block::where('key', 'offers')->firstOrFail();

        $items = $block->items()
            ->where('category_id', $category->id)
            ->with(['propertyValues.property']) // подгружаем связи
            ->get();

        return response()->json([
            'category' => $category->name,
            'block' => $block->name,
            'items' => BlockItemResource::collection($items),
        ]);
    }

    public function structure(?string $slug = null)
    {
        $query = BlocksCategories::with('childrenRecursive')
            ->whereNull('parent_id');

        if ($slug) {
            $query = BlocksCategories::where('key', $slug)->with('childrenRecursive');
        }

        $categories = $query->get();

        return BlockCategoryStructureResource::collection($categories);
    }

}
