<?php

namespace Tests\Feature;

use App\Livewire\CommentManager;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CommentModerationTest extends TestCase
{
    use RefreshDatabase;

    private Post $post;

    protected function setUp(): void
    {
        parent::setUp();
        $this->post = Post::create(['title' => 'Mon article', 'slug' => 'mon-article', 'content' => '<p>x</p>', 'user_id' => User::factory()->create()->id]);
    }

    private function comment(array $attributes = []): Comment
    {
        return Comment::create(array_merge([
            'post_id' => $this->post->id, 'name' => 'Visiteur', 'email' => 'visiteur@example.com',
            'comment' => 'Super article', 'approved' => false,
        ], $attributes));
    }

    public function test_page_requires_login(): void
    {
        $this->get('/comments/index')->assertRedirect(route('login'));
    }

    public function test_admin_sees_pending_comments_with_count_in_menu(): void
    {
        $this->comment(['comment' => 'En attente de validation']);
        $this->comment(['comment' => 'Déjà publié', 'approved' => true]);

        $this->actingAs(User::factory()->create())
            ->get('/comments/index')
            ->assertOk()
            ->assertSee('En attente de validation')
            ->assertDontSee('Déjà publié')
            ->assertSee('(1 en attente)');
    }

    public function test_filters(): void
    {
        $this->comment(['comment' => 'Commentaire en attente']);
        $this->comment(['comment' => 'Commentaire publié', 'approved' => true]);
        $this->actingAs(User::factory()->create());

        Livewire::test(CommentManager::class)
            ->set('filter', 'approved')
            ->assertSee('Commentaire publié')->assertDontSee('Commentaire en attente')
            ->set('filter', 'all')
            ->assertSee('Commentaire publié')->assertSee('Commentaire en attente')
            ->set('search', 'attente')
            ->assertSee('Commentaire en attente')->assertDontSee('Commentaire publié');
    }

    public function test_approved_comment_appears_on_the_public_api(): void
    {
        $comment = $this->comment(['comment' => 'À valider']);
        $this->getJson('/api/posts/mon-article')->assertJsonCount(0, 'post.comments');

        $this->actingAs(User::factory()->create());
        Livewire::test(CommentManager::class)->call('approve', $comment->id);

        $this->getJson('/api/posts/mon-article')
            ->assertJsonCount(1, 'post.comments')
            ->assertJsonPath('post.comments.0.comment', 'À valider')
            ->assertJsonPath('post.comments_count', 1);
    }

    public function test_unapprove_hides_comment_without_deleting_it(): void
    {
        $comment = $this->comment(['approved' => true]);
        $this->actingAs(User::factory()->create());

        Livewire::test(CommentManager::class)->call('unapprove', $comment->id);

        $this->assertFalse($comment->fresh()->approved);
        $this->getJson('/api/posts/mon-article')->assertJsonCount(0, 'post.comments');
    }

    public function test_delete(): void
    {
        $comment = $this->comment();
        $this->actingAs(User::factory()->create());

        Livewire::test(CommentManager::class)->call('delete', $comment->id);

        $this->assertModelMissing($comment);
    }

    public function test_comment_html_is_escaped_in_back_office(): void
    {
        // Le texte est déjà passé par strip_tags à l'envoi ; le nom, lui, est affiché échappé
        $this->comment(['name' => '<script>alert(1)</script>']);

        $this->actingAs(User::factory()->create())
            ->get('/comments/index')
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
    }
}
