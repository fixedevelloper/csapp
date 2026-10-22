<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminApiAuthTest extends TestCase
{
    use RefreshDatabase;

    private function admin(string $type = 'admin'): User
    {
        return User::create([
            'name' => 'Admin', 'email' => 'admin@test.com', 'password' => 'secret-pass-1', 'user_type' => $type,
        ]);
    }

    protected function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear('admin@test.com|127.0.0.1');
    }

    public function test_admin_can_login_and_use_token(): void
    {
        $this->admin();

        $res = $this->postJson('/api/admin/login', ['email' => 'admin@test.com', 'password' => 'secret-pass-1'])
            ->assertOk()
            ->assertJsonStructure(['token', 'expires_at', 'user' => ['id', 'name', 'email', 'role']]);

        $this->withToken($res->json('token'))->getJson('/api/admin/me')
            ->assertOk()->assertJsonPath('data.email', 'admin@test.com');
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->admin();
        $this->postJson('/api/admin/login', ['email' => 'admin@test.com', 'password' => 'wrong'])
            ->assertStatus(422)->assertJsonPath('message', 'Email ou mot de passe incorrect.');
    }

    public function test_non_admin_cannot_login(): void
    {
        $this->admin('author');
        $this->postJson('/api/admin/login', ['email' => 'admin@test.com', 'password' => 'secret-pass-1'])
            ->assertStatus(422);
    }

    public function test_non_admin_token_is_refused_on_admin_routes(): void
    {
        $user = $this->admin('author');
        $this->actingAs($user, 'sanctum')->getJson('/api/admin/me')->assertForbidden();
    }

    public function test_admin_routes_require_authentication(): void
    {
        $this->getJson('/api/admin/me')->assertUnauthorized();
        $this->postJson('/api/admin/logout')->assertUnauthorized();
    }

    public function test_login_is_rate_limited(): void
    {
        $this->admin();
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/admin/login', ['email' => 'admin@test.com', 'password' => 'bad'])->assertStatus(422);
        }
        $this->postJson('/api/admin/login', ['email' => 'admin@test.com', 'password' => 'secret-pass-1'])->assertStatus(429);
    }

    public function test_logout_revokes_token(): void
    {
        $this->admin();
        $token = $this->postJson('/api/admin/login', ['email' => 'admin@test.com', 'password' => 'secret-pass-1'])->json('token');

        $this->withToken($token)->postJson('/api/admin/logout')->assertOk();
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
