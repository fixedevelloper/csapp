<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Str;
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Création d'un user admin si inexistant
        $password = env('ADMIN_PASSWORD') ?: Str::password(20);
        $user = User::firstOrCreate(
            ['email' => 'admin@creativsolutions.com'],
            [
                'name' => 'Admin',
                'user_type' => 'admin',
                'password' => bcrypt($password),
            ]
        );

        // Un mot de passe généré n'est affiché qu'à la création du compte : notez-le ou changez-le ensuite
        // depuis /admin/profil (ou avec : php artisan admin:password admin@creativsolutions.com)
        if ($user->wasRecentlyCreated) {
            $this->command?->info("Compte admin {$user->email} créé avec le mot de passe : {$password}");
        }

        // Catégories, tags et articles du blog
        $this->call(BlogSeeder::class);
    }
}
