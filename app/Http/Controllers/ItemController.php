<?php

namespace App\Http\Controllers;
use App\Models\DictionaryItem;
use App\Services\TransformService;
class ItemController extends Controller
{
    public function getItemBySlug($slug, TransformService $transformer)
    {
        return response()->json($transformer->transformItem(DictionaryItem::where('key', $slug)
            ->with(['properties'])
            ->firstOrFail()->toArray()));
    }
}
