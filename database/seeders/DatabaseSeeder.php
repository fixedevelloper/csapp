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
        $user = User::firstOrCreate(
            ['email' => 'admin@creativsolutions.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt(env('ADMIN_PASSWORD') ?: Str::password(20)),
            ]
        );

        // Catégories, tags et articles du blog
        $this->call(BlogSeeder::class);
    }
}
