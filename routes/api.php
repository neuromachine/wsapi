<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use \App\Http\Controllers\Api\BlockCategoryController;

Route::get('/test', function () {
    $users = DB::table('users')->get();
    return response()->json($users);
});

Route::get('/dictionaries/{id}/categories/tree', [CategoryController::class, 'fullTree']);
Route::get('/categories/{slug}/items', [CategoryController::class, 'items']);
Route::get('/tree/{slug?}', [CategoryController::class, 'index']);
Route::get('/dictionaries/{key}/items', [DictionaryController::class, 'items']);

Route::get('/item/{slug}', [ItemController::class, 'getItemBySlug']);
Route::get('/cat/{slug}', [CategoryController::class, 'show']);

//Route::get('/group/offers/{slug}', [CategoryController::class, 'offers']);


Route::get('/group/offers/{slug}', [BlockCategoryController::class, 'offers']);

Route::get('blocks/structure/{slug?}', [BlockCategoryController::class, 'structure']);
