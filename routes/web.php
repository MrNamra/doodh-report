<?php

use App\Http\Controllers\AccoutingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SharingController;
use App\Http\Controllers\TrangactionController;
use Illuminate\Support\Facades\Route;

// Route::get('/register', []);
Route::get('/', [AuthController::class,'index'])->name('login');
Route::post('/', [AuthController::class,'login'])->name('user.login');
Route::get('/register', [AuthController::class,'registerPage'])->name('register');
Route::post('/register', [AuthController::class,'register'])->name('user.register');

Route::group(['middleware'=> 'auth'], function(){
    Route::post('/logout', [AuthController::class,'logout'])->name('user.logout');

    // add user
    Route::get('/account', [AccoutingController::class,'index'])->name('user.account');
    Route::post('/account', [AccoutingController::class,'addMoney'])->name('user.add.person');
    Route::post('/account-delete', [AccoutingController::class,'removeAccout'])->name('user.remove.person');

    // add records
    Route::get('/trangaction', [TrangactionController::class, 'index'])->name('user.record');
    Route::post('/trangaction', [TrangactionController::class, 'addTrangaction'])->name('user.add.record');
    
    Route::get('/show-trangaction', [TrangactionController::class, 'list'])->name('user.show.record');
    Route::post('/delete-trangaction', [TrangactionController::class, 'removeTrangaction'])->name('user.remove.record');
    
    Route::get('/share', [SharingController::class, 'index'])->name('share.link');
    Route::post('/get-share', [SharingController::class, 'getSharedLink'])->name('get.link');
    Route::post('/delete-link', [SharingController::class, 'removeSharedLink'])->name('remove.link');
});
Route::get('/c/{uuid}', [SharingController::class, 'getReport'])->name('share.link.uuid');