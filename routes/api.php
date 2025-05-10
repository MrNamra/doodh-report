<?php

use App\Http\Controllers\AccoutingController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/regiister', [AuthController::class, 'regiister']);

Route::group(['prefix' => ''], function () {
    
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    Route::post('logout', [AuthController::class,'logout']);

    Route::post('add-money', [AccoutingController::class,'addMoney']);
    Route::post('add-trangcation', [AccoutingController::class,'addTrangcation']);
})->middleware('auth:sanctum');

