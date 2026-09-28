<?php

namespace App\Http\Controllers\Auth;


use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login()
    {
        return view('auth.login');
    }


    public function loginstore(LoginRequest $loginRequest)
    {
        $loginRequest->authenticate();
        $loginRequest->session()->regenerate();
        return redirect("dashboard");
    }

    public function profil(Request $request)
    {
        $user = Auth::user();
        if ($request->method() == "POST") {
            $validated = $request->validate([
                'name'  => 'required|string|max:255',
                'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
                'phone' => 'nullable|string|max:20',
            ]);
            $user->update($validated);
            return redirect()->route('profil')->with('success', 'Profil mis à jour.');
        }
        return view('auth.profil', ['user' => $user]);
    }

    public function changeimage(Request $request)
    {
        $user = Auth::user();
        if ($request->method() == "POST") {
            $request->validate([
                'photo' => 'required|image|mimes:jpg,png,jpeg,webp|max:2048',
            ]);
            // Même collection que l'avatar des auteurs renvoyé par l'API du blog
            $user->addMediaFromRequest('photo')->toMediaCollection('avatars');
            return redirect()->route('profil')->with('success', 'Photo de profil mise à jour.');
        }
        return redirect()->route('profil');
    }

    public function changepassword(Request $request)
    {
        $request->validate([
            'oldpassword' => 'required|current_password',
            'password'    => ['required', 'confirmed', Password::min(8)],
        ], [
            'oldpassword.current_password' => 'L’ancien mot de passe est incorrect.',
        ]);

        $request->user()->update(['password' => $request->password]);

        return redirect()->route('profil')->with('success', 'Mot de passe modifié.');
    }

    /**
     * Destroy an authenticated session.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
