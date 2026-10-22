<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminModerationApiTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::create(['name' => 'Admin', 'email' => 'a@test.com', 'password' => 'secret-pass-1', 'user_type' => 'admin']);
        $this->post = Post::create(['title' => 'Mon article', 'slug' => 'mon-article', 'content' => '<p>x</p>', 'user_id' => $admin->id]);
        $this->actingAs($admin, 'sanctum');
    }

    private function comment(array $o = []): Comment
    {
        return Comment::create($o + ['post_id' => $this->post->id, 'name' => 'Visiteur', 'email' => 'v@x.com', 'comment' => 'Super article', 'approved' => false]);
    }

    public function test_requires_admin(): void
    {
        $author = User::create(['name' => 'B', 'email' => 'b@test.com', 'password' => 'secret-pass-1', 'user_type' => 'author']);
        $this->actingAs($author, 'sanctum');
        $this->getJson('/api/admin/comments')->assertForbidden();
        $this->getJson('/api/admin/newsletter')->assertForbidden();
        $this->getJson('/api/admin/newsletter/export')->assertForbidden();
    }

    public function test_comments_filters_counts_moderation_and_delete(): void
    {
        $pending = $this->comment();
        $this->comment(['approved' => true, 'name' => 'Approuvé']);

        $this->getJson('/api/admin/comments?filter=pending')->assertOk()->assertJsonCount(1, 'data')
            ->assertJsonPath('counts.pending', 1)->assertJsonPath('counts.approved', 1)->assertJsonPath('counts.all', 2)
            ->assertJsonPath('data.0.post.slug', 'mon-article');
        $this->getJson('/api/admin/comments?filter=all')->assertJsonCount(2, 'data');
        $this->getJson('/api/admin/comments?filter=approved&q=Approuvé')->assertJsonCount(1, 'data');
        $this->getJson('/api/admin/comments?filter=hack')->assertStatus(422);

        $this->patchJson("/api/admin/comments/{$pending->id}", ['approved' => true])->assertOk()->assertJsonPath('data.approved', true);
        $this->patchJson("/api/admin/comments/{$pending->id}", ['approved' => false])->assertOk()->assertJsonPath('data.approved', false);
        $this->patchJson("/api/admin/comments/{$pending->id}", ['approved' => 'peut-etre'])->assertStatus(422);
        $this->patchJson("/api/admin/comments/{$pending->id}", ['approved' => true, 'comment' => 'Piraté'])->assertOk();
        $this->assertSame('Super article', $pending->fresh()->comment);

        $this->deleteJson("/api/admin/comments/{$pending->id}")->assertOk();
        $this->assertDatabaseCount('comments', 1);
    }

    public function test_approved_comment_appears_on_public_api_only_after_approval(): void
    {
        $c = $this->comment();
        $this->getJson('/api/posts/mon-article')->assertJsonCount(0, 'post.comments');
        $this->patchJson("/api/admin/comments/{$c->id}", ['approved' => true])->assertOk();
        $this->getJson('/api/posts/mon-article')->assertJsonCount(1, 'post.comments');
    }

    public function test_newsletter_list_search_delete_and_csv_export(): void
    {
        NewsletterSubscriber::create(['email' => 'alice@example.com']);
        NewsletterSubscriber::create(['email' => 'bob@example.com']);
        $bob = NewsletterSubscriber::where('email', 'bob@example.com')->first();

        $this->getJson('/api/admin/newsletter')->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('counts.all', 2);
        $this->getJson('/api/admin/newsletter?q=alice')->assertJsonCount(1, 'data');

        $csv = $this->get('/api/admin/newsletter/export')->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8')->streamedContent();
        $this->assertStringContainsString("email,date_inscription", $csv);
        $this->assertStringContainsString('alice@example.com', $csv);

        $this->deleteJson("/api/admin/newsletter/{$bob->id}")->assertOk();
        $this->assertDatabaseCount('newsletter_subscribers', 1);
    }
}
