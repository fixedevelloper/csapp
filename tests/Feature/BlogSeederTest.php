<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Database\Seeders\BlogSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlogSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeds_ten_real_seo_articles_without_placeholder_text(): void
    {
        Storage::fake('public');
        User::factory()->create(['email' => 'admin@creativsolutions.com']);

        $this->seed(BlogSeeder::class);

        $this->assertSame(10, Post::count());
        foreach (Post::with('categories', 'tags')->get() as $post) {
            $this->assertStringNotContainsStringIgnoringCase('lorem', $post->content);
            $this->assertStringContainsString('<h2>', $post->content, $post->slug);
            $this->assertStringContainsString('href="/demandez-devis"', $post->content, $post->slug);
            $this->assertNotEmpty($post->meta_title);
            $this->assertLessThanOrEqual(60, mb_strlen($post->meta_title));
            $this->assertLessThanOrEqual(160, mb_strlen($post->meta_description));
            $this->assertNotEmpty($post->meta_keywords);
            $this->assertTrue($post->categories->isNotEmpty(), "{$post->slug} sans catégorie");
            $this->assertTrue($post->tags->isNotEmpty(), "{$post->slug} sans tag");
            $this->assertCount(1, $post->getMedia('posts'));
        }

        // Pas de faux commentaires présentés comme de vrais avis
        $this->assertSame(0, Comment::count());
    }

    public function test_running_twice_updates_without_duplicates(): void
    {
        Storage::fake('public');
        User::factory()->create(['email' => 'admin@creativsolutions.com']);

        $this->seed(BlogSeeder::class);
        Post::where('slug', 'marketing-digital-pour-pme')->update(['content' => 'ancien texte']);
        $this->seed(BlogSeeder::class);

        $this->assertSame(10, Post::count());
        $this->assertStringContainsString('WhatsApp Business', Post::where('slug', 'marketing-digital-pour-pme')->value('content'));
        $this->assertCount(1, Post::where('slug', 'marketing-digital-pour-pme')->first()->getMedia('posts'));
    }

    public function test_seo_fields_are_exposed_to_the_website(): void
    {
        Storage::fake('public');
        User::factory()->create(['email' => 'admin@creativsolutions.com']);
        $this->seed(BlogSeeder::class);

        $this->getJson('/api/posts/optimisation-seo-pour-les-entreprises-locales')
            ->assertOk()
            ->assertJsonPath('post.title', 'Optimisation SEO pour les entreprises locales au Cameroun')
            ->assertJsonPath('seo.description', 'Fiche Google Business, mots-clés locaux, contenus et avis clients : les leviers du référencement local pour attirer des clients près de chez vous.');
    }
}
