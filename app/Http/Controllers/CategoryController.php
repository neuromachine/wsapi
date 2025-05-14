<?php

namespace App\Http\Controllers;
use App\Models\DictionaryItemCategory;
use App\Services\CategoryService;
#use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(CategoryService $treeService)
    {
//        return response()->json($treeService->getTree());
//        sleep(3);
        return response()->json($treeService->getSubtreeByKey('portfolio'));
    }

    public function show(string $slug, CategoryService $treeService)
    {
        return response()->json($treeService->getSubtreeByKey($slug));
    }

    public function items($slug)
    {
        return response()->json(DictionaryItemCategory::where('key', $slug)
            ->with(['dictionaryItems.dictionary.properties'])
            ->firstOrFail());
    }
}
