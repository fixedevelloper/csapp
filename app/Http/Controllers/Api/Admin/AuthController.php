<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // Durée de vie du jeton d'administration, en heures (alignée sur le cookie côté Next.js)
    public const TOKEN_TTL_HOURS = 12;

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = Str::lower($credentials['email']).'|'.$request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);

            return response()->json([
                'message' => "Trop de tentatives de connexion. Réessayez dans {$seconds} secondes.",
            ], 429);
        }

        $user = User::where('email', $credentials['email'])->first();

        // Même message pour un mauvais mot de passe, un compte inconnu ou un compte non admin
        if (! $user || ! Hash::check($credentials['password'], $user->password) || $user->user_type !== 'admin') {
            RateLimiter::hit($key);

            return response()->json(['message' => 'Email ou mot de passe incorrect.'], 422);
        }

        RateLimiter::clear($key);

        $expiresAt = now()->addHours(self::TOKEN_TTL_HOURS);
        $token = $user->createToken('admin', ['admin'], $expiresAt)->plainTextToken;

        return response()->json([
            'token' => $token,
            'expires_at' => $expiresAt->toIso8601String(),
            'user' => new AdminUserResource($user),
        ]);
    }

    public function me(Request $request): AdminUserResource
    {
        return new AdminUserResource($request->user());
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnecté.']);
    }
}
