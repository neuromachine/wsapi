<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlocksCategories;
use App\Models\Block;

class BlockCategoryController extends Controller
{
    public function offers(string $slug)
    {
        $category = BlocksCategories::where('key', $slug)->firstOrFail();

        $block = Block::where('key', 'offers')->firstOrFail();

        $items = $block->items()
            ->where('category_id', $category->id)
            ->with(['block', 'category'])
            ->get();

        return response()->json([
            'category' => $category->name,
            'block' => $block->name,
            'items' => $items,
        ]);
    }

}
