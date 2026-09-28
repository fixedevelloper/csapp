<?php

namespace Tests\Feature;

use App\Livewire\CreatePost;
use App\Livewire\PostManager;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class BackOfficeTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_loads(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Admin Test']))
            ->get('/profil')
            ->assertOk()
            ->assertSee('Admin Test');
    }

    public function test_profile_can_be_updated(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/profil', ['name' => 'Nouveau Nom', 'email' => 'nouveau@example.com', 'phone' => '690000000'])
            ->assertRedirect(route('profil'))
            ->assertSessionHas('success');

        $user->refresh();
        $this->assertSame('Nouveau Nom', $user->name);
        $this->assertSame('nouveau@example.com', $user->email);
    }

    public function test_profile_email_must_be_unique(): void
    {
        User::factory()->create(['email' => 'pris@example.com']);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/profil', ['name' => 'X', 'email' => 'pris@example.com'])
            ->assertSessionHasErrors('email');
    }

    public function test_password_change_requires_current_password(): void
    {
        $user = User::factory()->create(['password' => 'ancien-mdp']);

        $this->actingAs($user)
            ->post('/changepassword', ['oldpassword' => 'faux', 'password' => 'nouveau-mdp', 'password_confirmation' => 'nouveau-mdp'])
            ->assertSessionHasErrors('oldpassword');

        $this->assertTrue(Hash::check('ancien-mdp', $user->fresh()->password));
    }

    public function test_password_can_be_changed(): void
    {
        $user = User::factory()->create(['password' => 'ancien-mdp']);

        $this->actingAs($user)
            ->post('/changepassword', ['oldpassword' => 'ancien-mdp', 'password' => 'nouveau-mdp', 'password_confirmation' => 'nouveau-mdp'])
            ->assertRedirect(route('profil'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('nouveau-mdp', $user->fresh()->password));
    }

    public function test_avatar_can_be_uploaded(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $this->assertStringContainsString('comment-avatar.png', $user->avatar_url);

        $this->actingAs($user)
            ->post('/changeimage', ['photo' => UploadedFile::fake()->image('moi.jpg', 300, 300)])
            ->assertRedirect(route('profil'));

        $this->assertCount(1, $user->fresh()->getMedia('avatars'));
        $this->assertStringNotContainsString('comment-avatar.png', $user->fresh()->avatar_url);
    }

    public function test_editing_a_post_keeps_its_author(): void
    {
        $author = User::factory()->create();
        $editor = User::factory()->create();
        $post = Post::create(['title' => 'Titre', 'slug' => 'titre', 'content' => '<p>Contenu initial</p>', 'user_id' => $author->id]);

        $this->actingAs($editor);
        Livewire::test(CreatePost::class, ['id' => $post->id])
            ->set('content', '<p>Contenu modifié par un autre</p>')
            ->call('save');

        $post->refresh();
        $this->assertSame($author->id, $post->user_id);
        $this->assertSame('<p>Contenu modifié par un autre</p>', $post->content);
    }

    public function test_post_can_be_deleted(): void
    {
        $post = Post::create(['title' => 'A supprimer', 'slug' => 'a-supprimer', 'content' => '<p>x</p>', 'user_id' => User::factory()->create()->id]);

        $this->actingAs(User::factory()->create());
        Livewire::test(PostManager::class)->call('delete', $post->id);

        $this->assertModelMissing($post);
    }
}
