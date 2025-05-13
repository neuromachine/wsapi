<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DictionaryItemCategory;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{

    public function itemsBySlug($slug)
    {
        $category = DictionaryItemCategory::where('key', $slug)
            ->firstOrFail();

//        dd($category);

        $items = $category->DictionaryItems()
            ->with(['dictionary.properties']) // подгружаем свойства через словарь
            ->get();

        dd($items);

        return response()->json([
            'category' => [
                'id' => $category->id,
                'name' => $category->name,
                'key' => $category->key,
            ],
            'items' => $items
        ]);
    }

    public function fullTree($dictionaryId)
    {
        $categories = DictionaryItemCategory::whereHas('DictionaryItems', function ($q) use ($dictionaryId) {
                $q->where('dictionary_id', $dictionaryId);
            })
            //->with('childrenRecursive', 'DictionaryItems')
            ->get();

        dd($categories);

        return response()->json([
            'data' => $categories
        ]);
    }
}
