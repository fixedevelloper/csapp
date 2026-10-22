<?php

use Illuminate\Support\Facades\Route;

/*
 * Le back-office est désormais dans l'application Next.js (/admin). Ces routes ne servent plus qu'à
 * rediriger les anciens liens vers les pages correspondantes ; le nom "login" reste défini car le
 * middleware d'authentification de Laravel s'y réfère.
 */
$admin = fn (string $path = '') => rtrim(config('app.admin_url'), '/').$path;

Route::get('/', fn () => redirect($admin()))->name('login');

foreach ([
    '/dashboard' => '',
    '/profil' => '/profil',
    '/posts/index' => '/articles',
    '/posts/create_edit' => '/articles/nouveau',
    '/categories/index' => '/categories',
    '/categories/tags' => '/categories',
    '/comments/index' => '/commentaires',
    '/newsletter/index' => '/newsletter',
] as $old => $new) {
    Route::get($old, fn () => redirect($admin($new)));
}
