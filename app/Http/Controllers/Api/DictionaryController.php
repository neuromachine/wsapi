<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DictionaryItemCategory;
use Illuminate\Http\Request;

class DictionaryController extends Controller
{
    public function fullTree($dictionaryId)
    {
        $categories = DictionaryItemCategory::whereNull('parent_id')
            ->whereHas('DictionaryItems', function ($q) use ($dictionaryId) {
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
