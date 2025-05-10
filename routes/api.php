<?php

use App\Http\Controllers\AccoutingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SharingController;
use App\Http\Controllers\TrangactionController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['name' => 'api.', 'middleware' => 'auth:sanctum'], function () {
    
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    Route::post('logout', [AuthController::class,'logout']);

    // add user
    Route::get('/account', [AccoutingController::class,'apiIndex'])->name('user.account');
    Route::post('/account', [AccoutingController::class,'addMoney'])->name('user.add.person');
    Route::post('/account-delete', [AccoutingController::class,'removeAccout'])->name('user.remove.person');

    Route::get('/trangaction', [TrangactionController::class, 'apiIndex'])->name('user.record');
    Route::post('/trangaction', [TrangactionController::class, 'addTrangaction'])->name('user.add.record');
    
    Route::get('/show-trangaction', [TrangactionController::class, 'apiList'])->name('user.show.record');
    Route::post('/delete-trangaction', [TrangactionController::class, 'removeTrangaction'])->name('user.remove.record');
    
    Route::get('/persons', [SharingController::class, 'apiIndex'])->name('allperson');
    Route::post('/get-share', [SharingController::class, 'getSharedLink'])->name('get.link');
    Route::post('/delete-link', [SharingController::class, 'removeSharedLink'])->name('remove.link');
});

