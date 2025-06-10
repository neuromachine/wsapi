<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlocksCategories;
use App\Models\Block;

use App\Repositories\BlockCategoryRepository;
use App\Http\Resources\BlockCategoryResource;
use App\Http\Resources\BlockItemResource;

use App\Http\Resources\BlockCategoryStructureResource;

class BlockCategoryController extends Controller
{
    protected BlockCategoryRepository $repo;

    // Репозиторий внедряется через DI
    public function __construct(BlockCategoryRepository $repo)
    {
        $this->repo = $repo;
    }

    public function index(string $slug)
    {
        return new BlockCategoryResource($this->repo->getCategory($slug));
    }

    public function structure(?string $slug = null)
    {
        return BlockCategoryStructureResource::collection($this->repo->getCategoriesRecursive($slug));
    }

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

}
