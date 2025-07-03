<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ItemController;
use \App\Http\Controllers\Api\BlockCategoryController;
use \App\Http\Controllers\Api\BlockController;
use \App\Http\Controllers\Api\BlockItemController;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
//sleep(3);

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


#Route::get('/group/offers/{slug}', [BlockCategoryController::class, 'offers']);

#Route::get('blocks/structure/{slug?}', [BlockCategoryController::class, 'structure']);

#Route::get('block-categories/{slug}/structure', [BlockCategoryController::class, 'structure']);

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
/*
        // Корневые действия без идентификатора
        Route::get('structure', 'rootStructure')->name('structure.root');
        Route::get('create', 'create')->name('create');

        // Если понадобится ID вместо slug
        Route::get('edit/{id}', 'edit')->name('edit');

        // Универсальный маршрут с GET-параметрами
        Route::get('search', 'search')->name('search');
        */
    });

Route::prefix('blocks/items')
    ->controller(BlockItemController::class)
    ->name('blocks.items.')
    ->group(function () {
        Route::get('{slug}', 'index')->name('index');
    });

Route::post('/offers/request', function (Request $request) {
    $itemId = $request->input('id');
    $contact = $request->input('contact');

    $item = DB::table('block_items')
        ->where('id', $itemId)
        ->first();

    // Формируем текст письма
    if ($item) {
        $message = "Запрос на предложение:\n\n";
        $message .= "ID позиции: {$item->id}\n";
        $message .= "Название: {$item->name}\n";
        $message .= "Контакт: {$contact}\n";
    } else {
        $message = "Запрос на предложение (позиция не найдена):\n\n";
        $message .= "Контакт: {$contact}\n";
    }

    // Отправка письма
    Mail::raw($message, function ($msg) {
        $msg->to('lili@ws-pro.ru')
            ->subject('Запрос предложения с сайта');
    });

    return response()->json(['status' => 'ok']);
});
