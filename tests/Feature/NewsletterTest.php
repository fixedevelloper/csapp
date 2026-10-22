<?php

namespace Tests\Feature;

use App\Livewire\NewsletterManager;
use App\Models\NewsletterSubscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitor_can_subscribe(): void
    {
        $this->postJson('/api/newsletter', ['email' => 'Client@Example.com'])
            ->assertOk()
            ->assertJsonStructure(['message']);

        $this->assertDatabaseHas('newsletter_subscribers', ['email' => 'client@example.com']);
    }

    public function test_subscribing_twice_does_not_duplicate_nor_reveal_existing_email(): void
    {
        $first = $this->postJson('/api/newsletter', ['email' => 'client@example.com'])->assertOk();
        $second = $this->postJson('/api/newsletter', ['email' => 'CLIENT@example.com'])->assertOk();

        $this->assertSame($first->json('message'), $second->json('message'));
        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }

    public function test_invalid_email_is_rejected(): void
    {
        $this->postJson('/api/newsletter', ['email' => 'pas-un-email'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_honeypot_rejects_bots(): void
    {
        $this->postJson('/api/newsletter', ['email' => 'bot@example.com', 'website' => 'http://spam.example'])
            ->assertUnprocessable();

        $this->assertDatabaseCount('newsletter_subscribers', 0);
    }
}
