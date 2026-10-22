<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Messages de l'API d'administration en français (le reste de l'application garde sa langue)
class AdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        app()->setLocale('fr');

        return $next($request);
    }
}
