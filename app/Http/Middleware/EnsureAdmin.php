<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Réserve l'API d'administration aux comptes de type "admin"
class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->user_type !== 'admin') {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return $next($request);
    }
}
