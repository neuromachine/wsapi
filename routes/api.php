<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

/*Route::get('/test', function () {
    $users = DB::table('users')->get();
    return response()->json($users);
});*/


Route::get('/test', function () {
    return response()->json([
        'status' => 'ok',
        'data' => ['one', 'two', 'three']
    ]);
});
