<?php

namespace App\Http\Controllers;
use App\Models\DictionaryItemCategory;
use App\Services\CategoryService;
#use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // TODO: не верное смысловое наименование методов - index,show
    public function index(CategoryService $categoryService,?string $slug = 'root')
    {
//        return response()->json($categoryService->getTree());
//        sleep(rand(2,3));
//        return response()->json($categoryService->getSubtreeByKey('portfolio'));
        return response()->json($categoryService->getSubtreeByKey($slug));
    }

    public function offers(CategoryService $categoryService, string $slug )
    {
        return response()->json($categoryService->getOffersForGroup($slug));
    }

    public function show(string $slug, CategoryService $categoryService)
    {
        return response()->json(DictionaryItemCategory::where('key', $slug)->firstOrFail());
    }

    public function items($slug)
    {
        return response()->json(DictionaryItemCategory::where('key', $slug)
            ->with(['dictionaryItems.dictionary.properties'])
            ->firstOrFail());
    }
}
