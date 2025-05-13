<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\DictionaryController;

Route::get('/test', function () {
    $users = DB::table('users')->get();
    return response()->json($users);
});

Route::get('/dictionaries/{id}/categories/tree', [DictionaryController::class, 'fullTree']);

Route::get('/categories/{slug}/items', [DictionaryController::class, 'itemsBySlug']);
