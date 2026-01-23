<?php

use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HookController;
use App\Http\Controllers\Api\PostController;

use Illuminate\Support\Facades\Route;

Route::get('categories', [HookController::class, 'getCategories']);
Route::get('tags', [HookController::class, 'getTags']);
Route::post('/posts/{post}', [PostController::class, 'update']);
Route::post('/posts/upload-image/{post}', [PostController::class, 'uploadEditorImage']);
Route::get('posts', [PostController::class, 'index']);
//Route::get('posts', [PostController::class, 'getPosts']);
Route::get('posts/latest', [PostController::class, 'latest']);
Route::get('posts/{slug}', [PostController::class, 'getPost']);
Route::get('posts/{id}/single', [PostController::class, 'getPostByID']);
Route::get('posts/category/{id}', [PostController::class, 'getCategoryByPosts']);

Route::post('/contact', [ContactController::class, 'store'])
    ->middleware('throttle:contact-form');
Route::post('/devis', [ContactController::class, 'storeDevis'])
    ->middleware('throttle:contact-form');
Route::post('/comments', [ContactController::class, 'storeComment'])->middleware('throttle:contact-form');;
