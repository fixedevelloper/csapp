<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Post;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Création d'un user admin si inexistant
        $user = User::firstOrCreate(
            ['email' => 'admin@creativsolutions.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password123'),
            ]
        );

        // Création des catégories
        $categories = collect([
            'E-commerce',
            'Développement Web',
            'Applications Mobiles',
            'Marketing Digital',
            'Design Graphique'
        ])->map(function($name){
            return Category::firstOrCreate(['name' => $name]);
        });

        // Création des tags
        $tags = collect([
            'SEO', 'UI/UX', 'Laravel', 'React', 'Mobile',
            'Social Media', 'Branding', 'Marketing', 'WordPress', 'Performance'
        ])->map(fn($name) => Tag::firstOrCreate(['name' => $name]));

        // Création des posts
        $postsData = [
            [
                'title' => 'Conseils long terme pour développer votre entreprise',
                'excerpt' => 'Découvrez des stratégies concrètes pour faire croître votre entreprise au Cameroun et en Afrique.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Conseils long terme pour développer votre entreprise'),
                'categories' => ['E-commerce', 'Marketing Digital'],
                'tags' => ['SEO', 'Marketing']
            ],
            [
                'title' => 'Stratégies gagnantes pour un marketing efficace',
                'excerpt' => 'Apprenez comment mettre en place des stratégies marketing performantes et adaptées au marché africain.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Stratégies gagnantes pour un marketing efficace'),
                'categories' => ['Marketing Digital', 'Développement Web'],
                'tags' => ['Social Media', 'Marketing']
            ],
            [
                'title' => 'Créer un site web professionnel pour votre business',
                'excerpt' => 'Nos conseils pour concevoir un site web attractif et fonctionnel, optimisé pour vos clients locaux.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Créer un site web professionnel pour votre business'),
                'categories' => ['Développement Web', 'E-commerce'],
                'tags' => ['Laravel', 'SEO']
            ],
            [
                'title' => 'Optimisation SEO pour les entreprises locales',
                'excerpt' => 'Améliorez la visibilité de votre entreprise sur Google au Cameroun et en Afrique.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Optimisation SEO pour les entreprises locales'),
                'categories' => ['Marketing Digital'],
                'tags' => ['SEO', 'Performance']
            ],
            [
                'title' => 'Conception d’applications mobiles attractives',
                'excerpt' => 'Découvrez comment créer des apps mobiles intuitives pour Android et iOS.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Conception d’applications mobiles attractives'),
                'categories' => ['Applications Mobiles', 'UI/UX'],
                'tags' => ['Mobile', 'UI/UX']
            ],
            [
                'title' => 'Design graphique professionnel pour votre marque',
                'excerpt' => 'Améliorez l’image de votre marque avec des designs percutants et modernes.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Design graphique professionnel pour votre marque'),
                'categories' => ['Design Graphique'],
                'tags' => ['Branding', 'UI/UX']
            ],
            [
                'title' => 'Développement d’e-commerce performant',
                'excerpt' => 'Créez une boutique en ligne optimisée pour vos clients locaux et internationaux.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Développement d’e-commerce performant'),
                'categories' => ['E-commerce', 'Développement Web'],
                'tags' => ['Laravel', 'Performance']
            ],
            [
                'title' => 'Marketing digital pour PME',
                'excerpt' => 'Des conseils pour booster votre visibilité et vos ventes.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Marketing digital pour PME'),
                'categories' => ['Marketing Digital'],
                'tags' => ['Marketing', 'Social Media']
            ],
            [
                'title' => 'Création d’une application web complète',
                'excerpt' => 'Tout ce que vous devez savoir pour créer une web app performante et attractive.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Création d’une application web complète'),
                'categories' => ['Développement Web', 'Applications Mobiles'],
                'tags' => ['Laravel', 'UI/UX']
            ],
            [
                'title' => 'Stratégie social media efficace',
                'excerpt' => 'Comment attirer et convertir vos clients via les réseaux sociaux.',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus at ligula nec enim...',
                'slug' => Str::slug('Stratégie social media efficace'),
                'categories' => ['Marketing Digital'],
                'tags' => ['Social Media', 'Marketing']
            ],
        ];

        foreach ($postsData as $postData) {
            $post = Post::create([
                'title' => $postData['title'],
                'slug' => $postData['slug'],
                'user_id' => $user->id,
                'excerpt' => $postData['excerpt'],
                'content' => $postData['content'],
            ]);

            // Attacher catégories
            $post->categories()->attach(
                Category::whereIn('name', $postData['categories'])->pluck('id')
            );

            // Attacher tags
            $post->tags()->attach(
                Tag::whereIn('name', $postData['tags'])->pluck('id')
            );

            // Ajouter 1 à 3 images (fictives)
            for ($i = 1; $i <= rand(1, 3); $i++) {
                $post->addMedia(storage_path("app/public/blog/600.jpeg"))
                    ->preservingOriginal()
                    ->toMediaCollection('posts');
            }
        }

        $this->command->info('✅ Blog posts, categories, tags et médias créés avec succès !');
    }
}
