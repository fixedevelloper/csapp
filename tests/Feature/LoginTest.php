<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_wrong_password_shows_french_error_and_keeps_email(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'bon-mot-de-passe']);

        $this->from('/')->post('/loginstore', ['email' => 'admin@example.com', 'password' => 'faux'])
            ->assertRedirect('/');

        $this->get('/')
            ->assertSee('Email ou mot de passe incorrect.')
            ->assertSee('value="admin@example.com"', false);
        $this->assertGuest();
    }

    public function test_valid_credentials_log_in(): void
    {
        $user = User::factory()->create(['password' => 'bon-mot-de-passe']);

        $this->post('/loginstore', ['email' => $user->email, 'password' => 'bon-mot-de-passe'])
            ->assertRedirect('dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_remember_me_sets_remember_cookie(): void
    {
        $user = User::factory()->create(['password' => 'bon-mot-de-passe']);

        $response = $this->post('/loginstore', ['email' => $user->email, 'password' => 'bon-mot-de-passe', 'remember' => '1']);

        $this->assertNotNull($user->fresh()->remember_token);
        $this->assertTrue(collect($response->headers->getCookies())->contains(fn ($c) => str_starts_with($c->getName(), 'remember_web_')));
    }

    public function test_expired_csrf_token_redirects_to_login_with_message_instead_of_419(): void
    {
        // Les tests désactivent normalement la vérification CSRF : on la force ici
        $this->app->bind(ValidateCsrfToken::class, fn ($app) => new class($app, $app['encrypter']) extends ValidateCsrfToken {
            protected function runningUnitTests() { return false; }
        });

        $this->post('/loginstore', ['_token' => 'jeton-perime', 'email' => 'moi@example.com', 'password' => 'x'])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['email' => 'Votre session a expiré. Veuillez réessayer.'])
            ->assertSessionHasInput('email', 'moi@example.com')
            ->assertSessionMissing('_old_input.password');
    }
}
