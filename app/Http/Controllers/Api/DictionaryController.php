<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DictionaryItemCategory;
use App\Models\Dictionary;
use Illuminate\Http\Request;
use App\Services\TransformService;

class DictionaryController extends Controller
{
    public function items($key, TransformService $transformer)
    {
        $dictionary = Dictionary::where('key', $key)
            ->with(['dictionaryitems.propertyValues.property'])
            ->firstOrFail()
            ->toArray();

        return response()->json(
            $transformer->transform($dictionary)
        );
    }

    public function itemsBySlug($slug)
    {
        $category = DictionaryItemCategory::where('key', $slug)
            ->with(['dictionaryItems.dictionary.properties'])
            ->firstOrFail();

        /*
        dd($category);


        $items = $category->DictionaryItems()
            ->with(['dictionary.properties']) // подгружаем свойства через словарь
            ->get();

        dd($items);
        */

        return response()->json([
            'data' => $category,
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
