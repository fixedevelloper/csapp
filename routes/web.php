<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'login'])
    ->name('login');
Route::post('/loginstore', [AuthController::class, 'loginstore'])
    ->name('loginstore');
Route::match(array('GET', 'POST'), '/profil', [AuthController::class, 'profil'])
    ->name('profil');
Route::get('/destroy', [AuthController::class, 'destroy'])
    ->name('destroy');
Route::match(array('GET', 'POST'), '/changeimage', [AuthController::class, 'changeimage'])
    ->name('changeimage');
Route::match(array('GET', 'POST'), '/dashboard', [DashboardController::class, 'dashboard'])
    ->name('dashboard');
Route::group(['prefix' => 'categories', 'as' => 'category.'],function () {
    Route::match(array('GET', 'POST'), 'index', [CategoryController::class, 'index'])
        ->name('index');
    Route::match(array('GET', 'POST'), 'tags', [CategoryController::class, 'tags'])
        ->name('tags');
});
Route::group(['prefix' => 'posts', 'as' => 'post.'],function () {
    Route::match(array('GET', 'POST'), 'index', [PostController::class, 'index'])
        ->name('index');
});
