<?php
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\BlockCategoryController;
use \App\Http\Controllers\Api\BlockController;
use \App\Http\Controllers\Api\BlockItemController;
use App\Http\Controllers\Api\FormController;

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
