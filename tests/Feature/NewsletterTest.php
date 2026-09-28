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

    public function test_back_office_pages_require_login(): void
    {
        $this->get('/newsletter/index')->assertRedirect(route('login'));
        $this->get('/newsletter/export')->assertRedirect(route('login'));
    }

    public function test_admin_sees_the_newsletter_page(): void
    {
        NewsletterSubscriber::create(['email' => 'a@example.com']);

        $this->actingAs(User::factory()->create())
            ->get('/newsletter/index')
            ->assertOk()
            ->assertSee('a@example.com')
            ->assertSee('Exporter en CSV');
    }

    public function test_admin_can_export_subscribers_as_csv(): void
    {
        NewsletterSubscriber::create(['email' => 'a@example.com']);
        NewsletterSubscriber::create(['email' => 'b@example.com']);

        $response = $this->actingAs(User::factory()->create())->get('/newsletter/export');

        $response->assertOk()->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $csv = $response->streamedContent();
        $this->assertStringStartsWith("email,date_inscription\n", $csv);
        $this->assertStringContainsString('a@example.com', $csv);
        $this->assertStringContainsString('b@example.com', $csv);
    }

    public function test_admin_can_search_and_delete_a_subscriber(): void
    {
        $keep = NewsletterSubscriber::create(['email' => 'garder@example.com']);
        $remove = NewsletterSubscriber::create(['email' => 'retirer@example.com']);

        $this->actingAs(User::factory()->create());

        Livewire::test(NewsletterManager::class)
            ->set('search', 'retirer')
            ->assertSee('retirer@example.com')
            ->assertDontSee('garder@example.com')
            ->call('delete', $remove->id);

        $this->assertModelMissing($remove);
        $this->assertModelExists($keep);
    }
}
