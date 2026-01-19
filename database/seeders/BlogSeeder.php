<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use App\Models\Tag;
use App\Models\Comment;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        // Création d'un user admin
        $user = User::firstOrCreate(
            ['email' => 'admin@creativsolutions.cm'],
            ['name' => 'Admin', 'password' => bcrypt('password')]
        );

        // 1️⃣ Categories
        $categories = ['Web Development', 'Graphic Design', 'Digital Marketing', 'UI/UX Design', 'Application Mobile'];
        $categories = collect($categories)->map(function($name) {
            return Category::firstOrCreate(['name' => $name]);
        });

        // 2️⃣ Tags
        $tags = ['Creativity', 'SEO', 'Strategy', 'Branding', 'Responsive', 'E-commerce', 'Mobile', 'UI', 'UX', 'Marketing'];
        $tags = collect($tags)->map(function($name) {
            return Tag::firstOrCreate(['name' => $name]);
        });

        // 3️⃣ Posts
        for ($i = 1; $i <= 10; $i++) {
            $post = Post::create([
                'title' => "Post $i: Conseils et Astuces Web",
                'slug' => "post-$i-conseils-web",
                'user_id' => $user->id,
                'excerpt' => "Résumé du post $i sur le marketing digital et la création web au Cameroun et en Afrique.",
                'content' => "Contenu détaillé du post $i expliquant comment améliorer votre présence en ligne, stratégie SEO, branding et développement d'applications web et mobile.",
                'meta_title' => "Post $i - Creativ Solutions | Web & Marketing",
                'meta_description' => "Découvrez le post $i de Creativ Solutions pour booster votre entreprise au Cameroun et en Afrique grâce au web, marketing et design.",
                'meta_keywords' => 'web, marketing, design, Cameroun, Afrique, creativ solutions',
            ]);

            // Assigner 1 à 3 catégories aléatoires
            $post->categories()->attach($categories->random(rand(1,3))->pluck('id')->toArray());

            // Assigner 2 à 4 tags aléatoires
            $post->tags()->attach($tags->random(rand(2,4))->pluck('id')->toArray());

            // Ajouter 1 à 3 commentaires
            for ($j = 1; $j <= rand(1,3); $j++) {
                Comment::create([
                    'name' => "Utilisateur $j",
                    'email' => "user{$j}@example.com",
                    'comment' => "Commentaire $j pour le post $i. Très utile et intéressant.",
                    'post_id' => $post->id,
                    'approved' => rand(0,1), // certains approuvés, d'autres non
                ]);
            }
        }
    }
}
