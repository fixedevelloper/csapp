<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\AdminUserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function update(Request $request): AdminUserResource
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ]);

        $user->update($data);

        return new AdminUserResource($user);
    }

    public function avatar(Request $request): AdminUserResource
    {
        $request->validate(['photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048']]);

        // Collection "avatars" en fichier unique : la nouvelle photo remplace l'ancienne
        $request->user()->addMediaFromRequest('photo')->toMediaCollection('avatars');

        return new AdminUserResource($request->user()->fresh());
    }

    public function destroyAvatar(Request $request): AdminUserResource
    {
        $request->user()->clearMediaCollection('avatars');

        return new AdminUserResource($request->user()->fresh());
    }

    public function password(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Password::min(8)],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'L’ancien mot de passe est incorrect.']);
        }

        $user->update(['password' => $data['password']]);

        // Les autres sessions d'administration (autres appareils) sont déconnectées
        $user->tokens()->where('id', '!=', $user->currentAccessToken()->id)->delete();

        return response()->json(['message' => 'Mot de passe modifié.']);
    }
}
