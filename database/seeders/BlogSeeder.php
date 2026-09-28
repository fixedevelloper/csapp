<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Facades\File;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Auteur des articles : le compte admin créé par DatabaseSeeder
        $author = User::where('email', 'admin@creativsolutions.com')->first()
            ?? User::where('user_type', 'admin')->first()
            ?? User::first();

        if (! $author) {
            $this->command?->error('Aucun utilisateur : lancez d’abord DatabaseSeeder (ou php artisan admin:password).');
            return;
        }

        // Catégories
        collect(['E-commerce', 'Développement Web', 'Applications Mobiles', 'Marketing Digital', 'Design Graphique'])
            ->each(fn ($name) => Category::firstOrCreate(['name' => $name]));

        // Tags
        collect(['SEO', 'UI/UX', 'Laravel', 'React', 'Mobile', 'Social Media', 'Branding', 'Marketing', 'WordPress', 'Performance'])
            ->each(fn ($name) => Tag::firstOrCreate(['name' => $name]));

        // Image de couverture par défaut
        $cover = storage_path('app/public/blog/600.jpeg');
        if (! File::exists($cover)) {
            File::ensureDirectoryExists(dirname($cover));
            File::copy(public_path('images/placeholder.jpg'), $cover);
        }

        // Articles : relancer le seeder met à jour les articles existants (même slug) sans créer de doublon
        foreach (require __DIR__ . '/data/articles.php' as $article) {
            $post = Post::updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'title' => $article['title'],
                    'user_id' => $author->id,
                    'excerpt' => $article['excerpt'],
                    'content' => $article['content'],
                    'meta_title' => $article['meta_title'],
                    'meta_description' => $article['meta_description'],
                    'meta_keywords' => $article['meta_keywords'],
                ]
            );

            $post->categories()->sync(Category::whereIn('name', $article['categories'])->pluck('id'));
            $post->tags()->sync(Tag::whereIn('name', $article['tags'])->pluck('id'));

            if (! $post->hasMedia('posts')) {
                $post->addMedia($cover)->preservingOriginal()->toMediaCollection('posts');
            }
        }

        $this->command?->info('✅ Blog : catégories, tags et articles créés ou mis à jour.');
    }
}
