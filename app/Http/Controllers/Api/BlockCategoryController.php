<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlocksCategories;
use App\Models\Block;
use App\Http\Resources\BlockCategoryResource;



use App\Repositories\BlockCategoryRepository;



use App\Http\Resources\BlockCategoryStructureResource;

use App\Http\Resources\BlockItemResource;


class BlockCategoryController extends Controller
{

    // Репозиторий внедряется через DI
    public function __construct(private BlockCategoryRepository $repo) {}


    /**
     * Возвращает полную структуру категории и вложенных блоков,
     * свойств и элементов
     */
    public function index(string $slug)
    {

        // 1) Repo вернёт "сырые" модели (с их связями)
        $category = $this->repo->getCategoryWithStructureBySlug($slug);

//        dd($category);

        // 2) Resource преобразует модель/коллекции в JSON
        return response()->json(new BlockCategoryResource($category));
    }

    /**
     *
     */
    public function structure(?string $slug = null)
    {
        if ($slug)
        {
            $query = BlocksCategories::where('key', $slug)->with('childrenRecursive');
        }
        else
        {
            $query = BlocksCategories::with('childrenRecursive')->whereNull('parent_id');
        }

        return BlockCategoryStructureResource::collection($query->get());
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
