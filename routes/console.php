<?php

use Illuminate\Foundation\Inspiring;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Définit (ou crée) un compte admin et son mot de passe, saisi sans être affiché.
// Utile si le mot de passe admin est perdu : php artisan admin:password admin@exemple.com
Artisan::command('admin:password {email}', function (string $email) {
    $password = $this->secret('Nouveau mot de passe (8 caractères minimum)');
    $confirmation = $this->secret('Confirmez le mot de passe');

    $validator = Validator::make(
        ['email' => $email, 'password' => $password, 'password_confirmation' => $confirmation],
        ['email' => 'required|email', 'password' => ['required', 'confirmed', Password::min(8)]]
    );
    if ($validator->fails()) {
        foreach ($validator->errors()->all() as $error) {
            $this->error($error);
        }
        return 1;
    }

    $user = User::where('email', $email)->first();
    if (! $user) {
        if (! $this->confirm("Aucun compte {$email}. Le créer comme administrateur ?", true)) {
            return 1;
        }
        $user = new User(['name' => 'Admin', 'email' => $email]);
    }

    $user->password = $password;
    $user->user_type = 'admin';
    $user->save();

    $this->info("Mot de passe mis à jour pour {$email} (compte administrateur).");
    return 0;
})->purpose('Définir le mot de passe d\'un compte administrateur');
