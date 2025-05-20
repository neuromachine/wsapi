<?php

namespace App\Services;

use App\Models\DictionaryItemCategory;

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
