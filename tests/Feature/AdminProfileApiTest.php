<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $email = 'a@test.com'): User
    {
        return User::create(['name' => 'Admin', 'email' => $email, 'password' => 'secret-pass-1', 'user_type' => 'admin']);
    }

    public function test_requires_admin(): void
    {
        $this->patchJson('/api/admin/profile', [])->assertUnauthorized();
        $author = User::create(['name' => 'B', 'email' => 'b@test.com', 'password' => 'secret-pass-1', 'user_type' => 'author']);
        $this->actingAs($author, 'sanctum')->putJson('/api/admin/profile/password', [])->assertForbidden();
    }

    public function test_update_profile_and_unique_email(): void
    {
        $admin = $this->admin();
        $this->admin('autre@test.com');
        $this->actingAs($admin, 'sanctum');

        $this->patchJson('/api/admin/profile', ['name' => 'Nouveau Nom', 'email' => 'a@test.com', 'phone' => '+237600000000'])
            ->assertOk()->assertJsonPath('data.name', 'Nouveau Nom')->assertJsonPath('data.phone', '+237600000000');
        $this->patchJson('/api/admin/profile', ['name' => 'X', 'email' => 'autre@test.com'])->assertStatus(422)->assertJsonValidationErrors('email');
        $this->patchJson('/api/admin/profile', ['name' => 'X', 'email' => 'pas-un-email'])->assertStatus(422);
        $this->patchJson('/api/admin/profile', ['name' => 'X', 'email' => 'a@test.com', 'user_type' => 'author'])->assertOk();
        $this->assertSame('admin', $admin->fresh()->user_type); // le rôle n'est pas modifiable ici
    }

    public function test_avatar_upload_and_removal(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin, 'sanctum');

        $this->post('/api/admin/profile/avatar', ['photo' => UploadedFile::fake()->image('moi.jpg', 300, 300)], ['Accept' => 'application/json'])->assertOk();
        $this->assertSame(1, $admin->fresh()->getMedia('avatars')->count());
        $this->post('/api/admin/profile/avatar', ['photo' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')], ['Accept' => 'application/json'])->assertStatus(422);

        $this->deleteJson('/api/admin/profile/avatar')->assertOk()->assertJsonPath('data.avatar', null);
        $this->assertSame(0, $admin->fresh()->getMedia('avatars')->count());
    }

    public function test_change_password_checks_current_and_revokes_other_tokens(): void
    {
        $admin = $this->admin();
        $current = $this->postJson('/api/admin/login', ['email' => 'a@test.com', 'password' => 'secret-pass-1'])->json('token');
        $other = $this->postJson('/api/admin/login', ['email' => 'a@test.com', 'password' => 'secret-pass-1'])->json('token');
        $this->app['auth']->forgetGuards();

        $this->withToken($current)->putJson('/api/admin/profile/password', ['current_password' => 'faux', 'password' => 'nouveau-pass-2', 'password_confirmation' => 'nouveau-pass-2'])
            ->assertStatus(422)->assertJsonValidationErrors('current_password');
        $this->app['auth']->forgetGuards();
        $this->withToken($current)->putJson('/api/admin/profile/password', ['current_password' => 'secret-pass-1', 'password' => 'court', 'password_confirmation' => 'court'])
            ->assertStatus(422)->assertJsonValidationErrors('password');
        $this->app['auth']->forgetGuards();
        $this->withToken($current)->putJson('/api/admin/profile/password', ['current_password' => 'secret-pass-1', 'password' => 'nouveau-pass-2', 'password_confirmation' => 'autre'])
            ->assertStatus(422);
        $this->app['auth']->forgetGuards();

        $this->withToken($current)->putJson('/api/admin/profile/password', ['current_password' => 'secret-pass-1', 'password' => 'nouveau-pass-2', 'password_confirmation' => 'nouveau-pass-2'])->assertOk();

        $this->assertTrue(Hash::check('nouveau-pass-2', $admin->fresh()->password));
        $this->assertDatabaseCount('personal_access_tokens', 1); // seule la session courante survit
        $this->app['auth']->forgetGuards();
        $this->withToken($current)->getJson('/api/admin/me')->assertOk();
        $this->app['auth']->forgetGuards();
        $this->withToken($other)->getJson('/api/admin/me')->assertUnauthorized();
    }
}
