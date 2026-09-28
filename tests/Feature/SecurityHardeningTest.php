<?php

namespace Tests\Feature;

use App\Mail\DevisSubmitted;
use App\Models\Devis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    private function devis(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Client',
            'email' => 'client@example.com',
            'phone' => '690000000',
            'project_type' => 'Site web',
            'description' => 'Un site vitrine',
        ], $overrides);
    }

    public function test_rate_limit_cannot_be_bypassed_by_changing_email(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/devis', $this->devis(['email' => "bot{$i}@example.com"]))->assertOk();
        }

        $this->postJson('/api/devis', $this->devis(['email' => 'bot6@example.com']))->assertTooManyRequests();
    }

    public function test_rate_limit_is_per_ip(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/devis', $this->devis())->assertOk();
        }

        // Un autre visiteur n'est pas bloqué par le premier
        $this->withServerVariables(['REMOTE_ADDR' => '198.51.100.7'])
            ->postJson('/api/devis', $this->devis())
            ->assertOk();
    }

    public function test_forwarded_for_header_is_ignored_without_trusted_proxy(): void
    {
        Mail::fake();

        foreach (range(1, 5) as $i) {
            $this->postJson('/api/devis', $this->devis(), ['X-Forwarded-For' => "203.0.113.{$i}"])->assertOk();
        }

        // Changer X-Forwarded-For ne permet pas de contourner la limite
        $this->postJson('/api/devis', $this->devis(), ['X-Forwarded-For' => '203.0.113.99'])->assertTooManyRequests();
    }

    public function test_long_messages_are_rejected(): void
    {
        Mail::fake();

        $this->postJson('/api/devis', $this->devis(['description' => str_repeat('a', 5001)]))
            ->assertJsonValidationErrors('description');
        $this->postJson('/api/contact', [
            'name' => 'Client', 'email' => 'client@example.com', 'phone' => '1', 'subject' => 'S',
            'message' => str_repeat('a', 5001),
        ])->assertJsonValidationErrors('message');
    }

    public function test_contact_honeypot_rejects_bots(): void
    {
        Mail::fake();

        $this->postJson('/api/contact', [
            'name' => 'Bot', 'email' => 'bot@example.com', 'phone' => '1', 'subject' => 'S', 'message' => 'spam',
            'website' => 'http://spam.example',
        ])->assertUnprocessable();

        Mail::assertNothingQueued();
    }

    public function test_devis_email_escapes_html_from_visitors(): void
    {
        $devis = Devis::create($this->devis([
            'name' => '<script>alert(1)</script>',
            'description' => '<img src=x onerror=alert(2)>',
        ]));

        $html = (new DevisSubmitted($devis))->render();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);
    }

    public function test_logout_requires_post(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/destroy')->assertMethodNotAllowed();
        $this->assertAuthenticated();

        $this->actingAs($user)->post('/destroy')->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_security_headers_are_sent(): void
    {
        $this->get('/')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $this->getJson('/api/posts')->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
