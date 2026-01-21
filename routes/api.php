<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\PostController;

use Illuminate\Support\Facades\Route;

Route::get('posts', [PostController::class, 'index']);
//Route::get('posts', [PostController::class, 'getPosts']);
Route::get('posts/latest', [PostController::class, 'latest']);
Route::get('posts/{slug}', [PostController::class, 'getPost']);
Route::get('posts/category/{id}', [PostController::class, 'getCategoryByPosts']);

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact-form');
Route::post('/devis', [ContactController::class, 'storeDevis'])
    ->middleware('throttle:contact-form');
