<?php

use App\Http\Controllers\Authcontroller;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [Authcontroller::class, 'index'])->name('login');
Route::post('/login', [Authcontroller::class, 'authenticate'])->name('login.authenticate');
Route::post('/logout', [Authcontroller::class, 'logout'])->name('logout');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('admin.home');
    Route::post('/store-event', [HomeController::class, 'storeEvent'])->name('admin.store.event');
    Route::post('/edit-event/{id}', [HomeController::class, 'updateEvent'])->name('admin.update.event');
    Route::delete('/edit-event/{id}', [HomeController::class, 'destroyEvent'])->name('admin.destroy.event');

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', [HomeController::class, 'editProfile'])->name('admin.editProfile');
        Route::post('/{id}', [HomeController::class, 'updateProfile'])->name('admin.updateProfile');
    });
});
