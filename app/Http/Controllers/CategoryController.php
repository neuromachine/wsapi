<?php

namespace App\Http\Controllers;
use App\Models\DictionaryItemCategory;
use App\Services\CategoryService;
#use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(CategoryService $categoryService)
    {
//        return response()->json($categoryService->getTree());
//        sleep(rand(3,10));
        return response()->json($categoryService->getSubtreeByKey('portfolio'));
    }

    public function show(string $slug, CategoryService $categoryService)
    {
        return response()->json($categoryService->getSubtreeByKey($slug));
    }

    public function items($slug)
    {
        return response()->json(DictionaryItemCategory::where('key', $slug)
            ->with(['dictionaryItems.dictionary.properties'])
            ->firstOrFail());
    }
}
