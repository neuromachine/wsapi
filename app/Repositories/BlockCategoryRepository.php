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

        dd(BlocksCategories::where('key', $key)->firstOrFail());

        return BlocksCategories::where('key', $key)->firstOrFail();

        // 2) Загружаем связанные блоки, их свойства и элементы.
        //    Можно добавить любые фильтры, например только активные блоки.
        $category->load([
//            'blocks' => function ($q) {
//                $q->where('is_active', true)
//                    ->orderBy('position', 'asc')
//                    ->with([
//                        // Свойства блока
//                        'properties' => function ($qp) {
//                            $qp->orderBy('order', 'asc');
//                        },
//                        // Элементы блока вместе с их значениями свойств
//                        'items' => function ($qi) {
//                            $qi->where('visible', true)
//                                ->orderBy('position', 'asc')
//                                ->with([
//                                    // Значения свойств каждого элемента
//                                    'itemProperties' => function ($qip) {
//                                        $qip->with('property');
//                                    }
//                                ]);
//                        }
//                    ]);
//            }
            'blocks' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }
        ]);

        return $category;
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
