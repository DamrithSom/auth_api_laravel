<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\BookController;
// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function (){
    Route::post('register',[AuthController::class, 'register'] );
    Route::post('login',[AuthController::class, 'login'] );
});

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('v1')->group(function(){
        Route::resource('books', BookController::class);
    });
});

