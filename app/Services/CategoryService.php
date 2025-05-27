<?php

namespace App\Services;

use App\Models\DictionaryItemCategory;
use App\Services\TransformService;

class CategoryService
{
    public function getTree(): array
    {
        $roots = DictionaryItemCategory::whereNull('parent_id')
            ->with('children')
            ->get();

        return $this->transformTree($roots);
    }

    public function getSubtreeByKey(string $key): array
    {
        $category = DictionaryItemCategory::where('key', $key)
            ->with('children')
//            ->with(['children' => function($q) {
//                $q->select('id', 'key', 'name','description');
//            }])
            ->firstOrFail();

        return $this->transformTree(collect([$category]))[0];
    }

    public function getOffersForGroup(string $key): array
    {

        //$category = DictionaryItemCategory::where('key', $key)->with('parent')->first();

        //dd($category?->parent);


        $category = DictionaryItemCategory::where('key', $key)
            ->whereHas('parent', function ($q) {
                $q->where('key', 'offer');
            })
            ->with(
                [
                    'children',
                    'dictionaryItems.propertyValues.property', // включая свойства
                ]
            )
            ->firstOrFail();

        //dd($category);

        return $this->transformCategoryWithItemsAndProperties($category);
    }



    public function transformCategoryWithItemsAndProperties(DictionaryItemCategory $category): array
    {
        $transformService = new TransformService();

        return [
            'id' => $category->id,
            'key' => $category->key,
            'name' => $category->name,
            'description' => $category->description ?? '',
            'content' => $category->content,
            'items' => $category->dictionaryItems->map(function ($item) use ($transformService) {
                // Сначала собираем свойства
                $rawItem = [
                    'id' => $item->id,
                    'key' => $item->key,
                    'name' => $item->name,
                    'description' => $item->description,
                    'properties' => $item->propertyValues->map(function ($propValue) {
                        return [
                            'key' => optional($propValue->property)->key,
                            'name' => optional($propValue->property)->name,
                            'value' => $propValue->value,
                        ];
                    })->toArray(),
                ];

                // Применяем трансформер
                return $transformService->transformItem($rawItem);
            })->toArray(),
            'children' => $category->children->map(function ($child) {
                return $this->transformCategoryWithItemsAndProperties($child);
            })->toArray(),
        ];
    }



    public function flattenIds(DictionaryItemCategory $category): array
    {
        $ids = [$category->id];

        foreach ($category->children as $child) {
            $ids = array_merge($ids, $this->flattenIds($child));
        }

        return $ids;
    }

    protected function transformTree($categories): array
    {
        return $categories->map(function ($category) {
            return [
                'id' => $category->id,
                'key' => $category->key,
                'name' => $category->name,
                'description' => $category->description ?? '',
                'content' => $category->content,
                'children' => $this->transformTree($category->children),
            ];
        })->toArray();
    }
}
