<?php
namespace App\Repositories;

use App\Models\BlocksCategories as BlocksCategory;
use Illuminate\Database\Eloquent\Collection;

class BlockCategoryRepository
{
    /**
     * Возвращает модель BlocksCategory со всеми связанными блоками,
     * свойствами блоков и элементами с их свойствами.
     *
     * @param string $key Уникальный идентификатор категории
     * @return BlocksCategory
     */
    public function getCategoryWithStructureBySlug(string $key): BlocksCategory
    {

        // 1) Сначала находим категорию
        $category = BlocksCategory::where('key', $key)->firstOrFail();

        return $category;
        //dd($category);

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

    /**
     * Пример: получить список всех категорий вместе с количеством блоков
     *
     * @return Collection
     */
    public function getAllWithBlockCount(): Collection
    {
        return BlocksCategory::withCount('blocks')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Пример: найти категорию по ID с проверкой, что в ней есть активный блок
     *
     * @param int $id
     * @return BlocksCategory|null
     */
    public function findActiveCategoryWithBlocks(int $id): ?BlocksCategory
    {
        return BlocksCategory::where('id', $id)
            ->whereHas('blocks', fn($q) => $q->where('is_active', true))
            ->first();
    }
}
