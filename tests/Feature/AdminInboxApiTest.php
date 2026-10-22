<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Devis;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInboxApiTest extends TestCase
{
    use RefreshDatabase;

    private function login(string $type = 'admin'): static
    {
        $user = User::create(['name' => 'A', 'email' => "a{$type}@test.com", 'password' => 'secret-pass-1', 'user_type' => $type]);

        return $this->actingAs($user, 'sanctum');
    }

    private function devis(array $o = []): Devis
    {
        return Devis::create($o + ['name' => 'Jean', 'email' => 'j@x.com', 'phone' => '600', 'project_type' => 'Site web', 'description' => 'Un site vitrine']);
    }

    private function contact(array $o = []): Contact
    {
        return Contact::create($o + ['name' => 'Awa', 'email' => 'a@x.com', 'phone' => '600', 'subject' => 'Question', 'message' => 'Bonjour']);
    }

    public function test_endpoints_require_admin(): void
    {
        $this->getJson('/api/admin/devis')->assertUnauthorized();
        $this->getJson('/api/admin/contacts')->assertUnauthorized();
        $this->getJson('/api/admin/stats')->assertUnauthorized();
        $this->login('author')->getJson('/api/admin/devis')->assertForbidden();
        $this->login('manager')->deleteJson('/api/admin/contacts/1')->assertForbidden();
    }

    public function test_devis_list_filters_search_and_counts(): void
    {
        $this->devis(['name' => 'Alice']);
        $this->devis(['name' => 'Bob', 'status' => 'accepte']);
        $this->login();

        $this->getJson('/api/admin/devis')->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('counts.all', 2)->assertJsonPath('counts.nouveau', 1)->assertJsonPath('counts.accepte', 1);

        $this->getJson('/api/admin/devis?status=accepte')->assertJsonCount(1, 'data')->assertJsonPath('data.0.name', 'Bob');
        $this->getJson('/api/admin/devis?q=Alice')->assertJsonCount(1, 'data');
        $this->getJson('/api/admin/devis?status=hack')->assertStatus(422);
    }

    public function test_devis_status_update_and_delete(): void
    {
        $d = $this->devis();
        $this->login();

        $this->patchJson("/api/admin/devis/{$d->id}", ['status' => 'en_cours'])->assertOk()->assertJsonPath('data.status', 'en_cours');
        $this->patchJson("/api/admin/devis/{$d->id}", ['status' => 'nimporte'])->assertStatus(422);
        $this->patchJson("/api/admin/devis/{$d->id}", ['name' => 'Piraté', 'status' => 'envoye'])->assertOk();
        $this->assertSame('Jean', $d->fresh()->name);

        $this->deleteJson("/api/admin/devis/{$d->id}")->assertOk();
        $this->assertDatabaseCount('devis', 0);
        $this->getJson("/api/admin/devis/{$d->id}")->assertNotFound();
    }

    public function test_contacts_list_unread_filter_read_toggle_and_delete(): void
    {
        $unread = $this->contact();
        $this->contact(['is_read' => true, 'subject' => 'Autre']);
        $this->login();

        $this->getJson('/api/admin/contacts')->assertOk()->assertJsonCount(2, 'data')
            ->assertJsonPath('counts.unread', 1)->assertJsonPath('counts.all', 2);
        $this->getJson('/api/admin/contacts?unread=1')->assertJsonCount(1, 'data');
        $this->getJson('/api/admin/contacts?q=Autre')->assertJsonCount(1, 'data');

        $this->patchJson("/api/admin/contacts/{$unread->id}", ['is_read' => true])->assertOk()->assertJsonPath('data.is_read', true);
        $this->deleteJson("/api/admin/contacts/{$unread->id}")->assertOk();
        $this->assertDatabaseCount('contacts', 1);
    }

    public function test_stats(): void
    {
        $this->devis();
        $this->devis(['status' => 'accepte']);
        $this->contact();
        $this->login();

        $this->getJson('/api/admin/stats')->assertOk()->assertExactJson([
            'devis_new' => 1, 'devis_total' => 2, 'contacts_unread' => 1, 'contacts_total' => 1, 'comments_pending' => 0, 'newsletter_total' => 0,
        ]);
    }
}
