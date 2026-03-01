<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use \App\Http\Controllers\Api\BlockCategoryController;
use \App\Http\Controllers\Api\BlockController;
use \App\Http\Controllers\Api\BlockItemController;
use App\Http\Controllers\Api\FormController;

Route::get('/dictionaries/{id}/categories/tree', [CategoryController::class, 'fullTree']);
Route::get('/categories/{slug}/items', [CategoryController::class, 'items']);
Route::get('/tree/{slug?}', [CategoryController::class, 'index']);
Route::get('/dictionaries/{key}/items', [DictionaryController::class, 'items']);

Route::get('/item/{slug}', [ItemController::class, 'getItemBySlug']);
Route::get('/cat/{slug}', [CategoryController::class, 'show']);

Route::prefix('blocks/blocks')
    ->controller(BlockController::class)
    ->name('blocks.blocks.')
    ->group(function () {
        Route::get('{slug}', 'index')->name('index');
    });

Route::prefix('blocks/categories')
    ->controller(BlockCategoryController::class)
    ->name('blocks.categories.')
    ->group(function () {
        Route::get('{slug}', 'index')->name('index');
        Route::get('structure/{slug?}', 'structure')->name('structure');
        Route::get('offers/{slug}', 'offers')->name('offers');
    });

Route::prefix('blocks/items')
    ->controller(BlockItemController::class)
    ->name('blocks.items.')
    ->group(function () {
        Route::get('{slug}', 'index')->name('index');
    });

Route::post('/forms/submit', [FormController::class, 'store']);
