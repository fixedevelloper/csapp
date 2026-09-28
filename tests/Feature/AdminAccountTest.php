<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_resets_password_of_existing_admin_who_can_then_log_in(): void
    {
        $admin = User::factory()->create(['email' => 'admin@example.com', 'password' => 'mot-de-passe-perdu']);

        $this->artisan('admin:password', ['email' => 'admin@example.com'])
            ->expectsQuestion('Nouveau mot de passe (8 caractères minimum)', 'nouveau-secret-42')
            ->expectsQuestion('Confirmez le mot de passe', 'nouveau-secret-42')
            ->expectsOutputToContain('Mot de passe mis à jour')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('nouveau-secret-42', $admin->fresh()->password));

        $this->post('/loginstore', ['email' => 'admin@example.com', 'password' => 'nouveau-secret-42'])
            ->assertRedirect('dashboard');
        $this->assertAuthenticatedAs($admin);
    }

    public function test_command_can_create_missing_admin(): void
    {
        $this->artisan('admin:password', ['email' => 'nouvel-admin@example.com'])
            ->expectsQuestion('Nouveau mot de passe (8 caractères minimum)', 'nouveau-secret-42')
            ->expectsQuestion('Confirmez le mot de passe', 'nouveau-secret-42')
            ->expectsConfirmation('Aucun compte nouvel-admin@example.com. Le créer comme administrateur ?', 'yes')
            ->assertSuccessful();

        $user = User::where('email', 'nouvel-admin@example.com')->first();
        $this->assertSame('admin', $user->user_type);
        $this->assertTrue(Hash::check('nouveau-secret-42', $user->password));
    }

    public function test_command_rejects_mismatched_or_short_password(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'ancien-secret-42']);

        $this->artisan('admin:password', ['email' => 'admin@example.com'])
            ->expectsQuestion('Nouveau mot de passe (8 caractères minimum)', 'court')
            ->expectsQuestion('Confirmez le mot de passe', 'different')
            ->assertFailed();

        $this->assertTrue(Hash::check('ancien-secret-42', User::first()->password));
    }

    public function test_seeder_creates_admin_with_type_and_displays_generated_password(): void
    {
        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class])
            ->expectsOutputToContain('Compte admin admin@creativsolutions.com créé avec le mot de passe')
            ->assertSuccessful();

        $this->assertSame('admin', User::where('email', 'admin@creativsolutions.com')->value('user_type'));
    }

    public function test_seeder_does_not_touch_existing_admin_password(): void
    {
        $admin = User::factory()->create(['email' => 'admin@creativsolutions.com', 'password' => 'mon-vrai-mdp-42']);

        $this->artisan('db:seed', ['--class' => DatabaseSeeder::class])->assertSuccessful();

        $this->assertTrue(Hash::check('mon-vrai-mdp-42', $admin->fresh()->password));
    }
}
