<?php

use App\Http\Controllers\Api\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Api\Admin\CommentController as AdminCommentController;
use App\Http\Controllers\Api\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Api\Admin\DevisController as AdminDevisController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\NewsletterController as AdminNewsletterController;
use App\Http\Controllers\Api\Admin\PostController as AdminPostController;
use App\Http\Controllers\Api\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Api\Admin\StatsController as AdminStatsController;
use App\Http\Controllers\Api\Admin\TagController as AdminTagController;
use App\Http\Controllers\Api\Admin\UploadController as AdminUploadController;
use App\Http\Controllers\Api\ContactController;
use App\Http\Controllers\Api\HookController;
use App\Http\Controllers\Api\NewsletterController;
use App\Http\Controllers\Api\PostController;

use Illuminate\Support\Facades\Route;

Route::get('categories', [HookController::class, 'getCategories']);
Route::get('tags', [HookController::class, 'getTags']);
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
Route::post('/comments', [ContactController::class, 'storeComment'])->middleware('throttle:contact-form');
Route::post('/newsletter', [NewsletterController::class, 'subscribe'])
    ->middleware('throttle:contact-form');

// API d'administration consommée par le back-office Next.js (/admin)
Route::prefix('admin')->middleware('admin.locale')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login'])->middleware('throttle:20,1');
    Route::middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::get('me', [AdminAuthController::class, 'me']);
        Route::post('logout', [AdminAuthController::class, 'logout']);
        Route::get('stats', AdminStatsController::class);
        // Mise à jour d'un article en multipart : POST + champ _method=PATCH (PHP ne lit pas le multipart en PATCH)
        Route::apiResource('posts', AdminPostController::class)->names('admin.posts');
        Route::post('uploads', AdminUploadController::class);
        Route::patch('profile', [AdminProfileController::class, 'update']);
        Route::post('profile/avatar', [AdminProfileController::class, 'avatar']);
        Route::delete('profile/avatar', [AdminProfileController::class, 'destroyAvatar']);
        Route::put('profile/password', [AdminProfileController::class, 'password'])->middleware('throttle:10,1');
        Route::apiResource('comments', AdminCommentController::class)->only(['index', 'update', 'destroy']);
        Route::get('newsletter', [AdminNewsletterController::class, 'index']);
        Route::get('newsletter/export', [AdminNewsletterController::class, 'export']);
        Route::delete('newsletter/{subscriber}', [AdminNewsletterController::class, 'destroy']);
        Route::apiResource('categories', AdminCategoryController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('tags', AdminTagController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::apiResource('devis', AdminDevisController::class)->only(['index', 'show', 'update', 'destroy'])->parameters(['devis' => 'devis']);
        Route::apiResource('contacts', AdminContactController::class)->only(['index', 'show', 'update', 'destroy']);
    });
});
