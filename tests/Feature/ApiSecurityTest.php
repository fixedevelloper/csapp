<?php

namespace Tests\Feature;

use App\Mail\ContactSubmitted;
use App\Mail\DevisSubmitted;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ApiSecurityTest extends TestCase
{
    use RefreshDatabase;

    // Pas de PostFactory : son afterCreating télécharge une image depuis internet
    private function makePost(array $attributes = []): Post
    {
        return Post::create(array_merge([
            'title' => 'Article de test',
            'slug' => 'article-de-test',
            'content' => '<p>Contenu</p>',
            'user_id' => User::factory()->create()->id,
        ], $attributes));
    }

    public function test_guest_cannot_update_a_post(): void
    {
        $post = $this->makePost();

        $this->postJson("/api/posts/{$post->id}", [
            'title' => 'Piraté',
            'content' => 'Piraté',
        ])->assertUnauthorized();

        $this->assertSame('Article de test', $post->fresh()->title);
    }

    public function test_guest_cannot_upload_an_editor_image(): void
    {
        $post = $this->makePost();

        $this->postJson("/api/posts/upload-image/{$post->id}")->assertUnauthorized();
    }

    public function test_authenticated_user_can_update_a_post_without_changing_its_slug(): void
    {
        $post = $this->makePost();
        Sanctum::actingAs(User::factory()->create());

        $this->postJson("/api/posts/{$post->id}", [
            'title' => 'Nouveau titre',
            'content' => '<p>Nouveau contenu</p>',
        ])->assertOk();

        $post->refresh();
        $this->assertSame('Nouveau titre', $post->title);
        $this->assertSame('article-de-test', $post->slug);
    }

    public function test_post_exposes_only_approved_comments_without_emails(): void
    {
        $post = $this->makePost();
        Comment::create(['post_id' => $post->id, 'name' => 'Visible', 'email' => 'visible@example.com', 'comment' => 'ok', 'approved' => true]);
        Comment::create(['post_id' => $post->id, 'name' => 'Masqué', 'email' => 'masque@example.com', 'comment' => 'spam', 'approved' => false]);

        $response = $this->getJson("/api/posts/{$post->slug}")->assertOk();

        $response->assertJsonPath('post.comments_count', 1)
            ->assertJsonCount(1, 'post.comments')
            ->assertJsonPath('post.comments.0.name', 'Visible')
            ->assertJsonMissingPath('post.comments.0.email');
        $this->assertStringNotContainsString('@example.com', $response->getContent());
    }

    public function test_back_office_redirects_guests_to_login(): void
    {
        foreach (['/dashboard', '/posts/index', '/posts/create_edit', '/categories/index', '/categories/tags', '/profil'] as $url) {
            $this->get($url)->assertRedirect(route('login'));
        }
    }

    public function test_unique_slug_is_suffixed_when_title_already_exists(): void
    {
        $this->makePost(['slug' => 'mon-titre']);

        $this->assertSame('mon-titre-2', Post::uniqueSlug('Mon titre'));
        $this->assertSame('autre-titre', Post::uniqueSlug('Autre titre'));
    }

    public function test_posts_limit_is_capped(): void
    {
        $this->getJson('/api/posts?limit=100000')
            ->assertOk()
            ->assertJsonPath('meta.per_page', 100);
    }

    public function test_contact_form_queues_email_to_configured_recipient(): void
    {
        Mail::fake();
        config(['mail.recipients.contact' => 'contact@test.local']);

        $this->postJson('/api/contact', [
            'name' => 'Client',
            'email' => 'client@example.com',
            'phone' => '690000000',
            'subject' => 'Demande',
            'message' => 'Bonjour',
        ])->assertOk();

        Mail::assertQueued(ContactSubmitted::class, fn ($mail) => $mail->hasTo('contact@test.local'));
    }

    public function test_devis_honeypot_rejects_bots(): void
    {
        Mail::fake();

        $this->postJson('/api/devis', [
            'name' => 'Bot',
            'email' => 'bot@example.com',
            'phone' => '690000000',
            'project_type' => 'site',
            'description' => 'spam',
            'website' => 'http://spam.example',
        ])->assertUnprocessable();

        Mail::assertNothingQueued();
        $this->assertDatabaseCount('devis', 0);
    }

    public function test_devis_form_queues_email(): void
    {
        Mail::fake();
        config(['mail.recipients.devis' => 'devis@test.local']);

        $this->postJson('/api/devis', [
            'name' => 'Client',
            'email' => 'client@example.com',
            'phone' => '690000000',
            'project_type' => 'Site vitrine',
            'description' => 'Un site pour mon entreprise',
        ])->assertOk();

        Mail::assertQueued(DevisSubmitted::class, fn ($mail) => $mail->hasTo('devis@test.local'));
    }
}
