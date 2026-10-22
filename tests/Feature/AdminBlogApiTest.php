<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminBlogApiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::create(['name' => 'Admin', 'email' => 'a@test.com', 'password' => 'secret-pass-1', 'user_type' => 'admin']);
    }

    private function asAdmin(): static
    {
        return $this->actingAs($this->admin, 'sanctum');
    }

    private function payload(array $o = []): array
    {
        return $o + ['title' => 'Créer un site web au Cameroun', 'content' => '<p>Contenu de l\'article avec assez de texte.</p>'];
    }

    public function test_blog_admin_endpoints_require_admin(): void
    {
        $this->getJson('/api/admin/posts')->assertUnauthorized();
        $author = User::create(['name' => 'B', 'email' => 'b@test.com', 'password' => 'secret-pass-1', 'user_type' => 'author']);
        $this->actingAs($author, 'sanctum')->postJson('/api/admin/posts', $this->payload())->assertForbidden();
        $this->actingAs($author, 'sanctum')->postJson('/api/admin/categories', ['name' => 'X1'])->assertForbidden();
        $this->actingAs($author, 'sanctum')->postJson('/api/admin/tags', ['name' => 'X1'])->assertForbidden();
        $this->actingAs($author, 'sanctum')->postJson('/api/admin/uploads')->assertForbidden();
    }

    public function test_create_post_with_relations_defaults_and_unique_slug(): void
    {
        Storage::fake('public');
        $cat = Category::create(['name' => 'Web']);
        $tag = Tag::create(['name' => 'SEO']);

        $res = $this->asAdmin()->postJson('/api/admin/posts', $this->payload(['categories' => [$cat->id], 'tags' => [$tag->id]]))
            ->assertCreated()
            ->assertJsonPath('data.slug', 'creer-un-site-web-au-cameroun')
            ->assertJsonPath('data.categories.0.name', 'Web')
            ->assertJsonPath('data.tags.0.name', 'SEO')
            ->assertJsonPath('data.meta_title', 'Créer un site web au Cameroun');

        $this->assertNotEmpty($res->json('data.excerpt'));
        $this->assertSame($this->admin->id, Post::first()->user_id);

        $this->asAdmin()->postJson('/api/admin/posts', $this->payload())->assertCreated()
            ->assertJsonPath('data.slug', 'creer-un-site-web-au-cameroun-2');
    }

    public function test_validation(): void
    {
        $this->asAdmin()->postJson('/api/admin/posts', ['title' => 'ab', 'content' => 'court'])->assertStatus(422)->assertJsonValidationErrors(['title', 'content']);
        $this->asAdmin()->postJson('/api/admin/posts', $this->payload(['categories' => [999]]))->assertStatus(422)->assertJsonValidationErrors(['categories.0']);
        $this->asAdmin()->postJson('/api/admin/posts', $this->payload(['image' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')]))->assertStatus(422);
    }

    public function test_update_keeps_slug_and_flushes_cache(): void
    {
        $this->asAdmin()->postJson('/api/admin/posts', $this->payload())->assertCreated();
        $post = Post::first();
        Cache::put("post:{$post->slug}", 'ancien');

        $this->asAdmin()->postJson("/api/admin/posts/{$post->id}", $this->payload(['title' => 'Nouveau titre complet', '_method' => 'PATCH']))
            ->assertOk()->assertJsonPath('data.title', 'Nouveau titre complet')->assertJsonPath('data.slug', 'creer-un-site-web-au-cameroun');

        $this->assertNull(Cache::get("post:{$post->slug}"));
    }

    public function test_index_search_and_category_filter_then_delete(): void
    {
        $cat = Category::create(['name' => 'Web']);
        $this->asAdmin()->postJson('/api/admin/posts', $this->payload(['categories' => [$cat->id]]))->assertCreated();
        $this->asAdmin()->postJson('/api/admin/posts', $this->payload(['title' => 'Un autre sujet marketing']))->assertCreated();

        $this->asAdmin()->getJson('/api/admin/posts')->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('counts.all', 2);
        $this->asAdmin()->getJson('/api/admin/posts?q=marketing')->assertJsonCount(1, 'data');
        $this->asAdmin()->getJson("/api/admin/posts?category={$cat->id}")->assertJsonCount(1, 'data');

        $post = Post::first();
        $this->asAdmin()->deleteJson("/api/admin/posts/{$post->id}")->assertOk();
        $this->assertDatabaseCount('posts', 1);
        $this->asAdmin()->getJson("/api/admin/posts/{$post->id}")->assertNotFound();
    }

    public function test_categories_and_tags_crud_with_unique_names(): void
    {
        foreach (['categories' => Category::class, 'tags' => Tag::class] as $path => $model) {
            $id = $this->asAdmin()->postJson("/api/admin/{$path}", ['name' => 'Alpha'])->assertCreated()->json('data.id');
            $this->asAdmin()->postJson("/api/admin/{$path}", ['name' => 'Alpha'])->assertStatus(422)->assertJsonPath('errors.name.0', 'Cette valeur existe déjà pour nom.');
            $this->asAdmin()->patchJson("/api/admin/{$path}/{$id}", ['name' => 'Beta'])->assertOk()->assertJsonPath('data.name', 'Beta');
            $this->asAdmin()->patchJson("/api/admin/{$path}/{$id}", ['name' => 'Beta'])->assertOk(); // même nom : autorisé
            $this->asAdmin()->getJson("/api/admin/{$path}")->assertOk()->assertJsonPath('data.0.posts_count', 0);
            $this->asAdmin()->deleteJson("/api/admin/{$path}/{$id}")->assertOk();
            $this->assertSame(0, $model::count());
        }
    }

    public function test_editor_image_upload(): void
    {
        Storage::fake('public');

        $res = $this->asAdmin()->postJson('/api/admin/uploads', ['image' => UploadedFile::fake()->image('photo.jpg', 600, 400)])->assertCreated();
        $this->assertStringContainsString('editor-images/', $res->json('url'));
        Storage::disk('public')->assertExists('editor-images/'.basename($res->json('url')));

        $this->asAdmin()->postJson('/api/admin/uploads', ['image' => UploadedFile::fake()->create('x.php', 5, 'text/x-php')])->assertStatus(422);
    }
}
