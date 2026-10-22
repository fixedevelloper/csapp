<?php

namespace Tests\Feature;

use App\Mail\DevisSubmitted;
use App\Models\Devis;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DevisGuidedFormTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $o = []): array
    {
        return array_merge([
            'name' => 'Client',
            'email' => 'client@example.com',
            'phone' => '690000000',
            'project_type' => 'Site web',
        ], $o);
    }

    public function test_guided_service_accepts_details_without_description(): void
    {
        Mail::fake();

        $this->postJson('/api/devis', $this->payload([
            'deadline' => '1 à 3 mois',
            'source' => '/services/creation-site-web',
            'details' => ['site_kind' => 'Nouveau site', 'features' => ['Blog', 'Multilingue']],
        ]))->assertOk();

        $d = Devis::firstOrFail();
        $this->assertNull($d->description);
        $this->assertSame('1 à 3 mois', $d->deadline);
        $this->assertSame(['Blog', 'Multilingue'], $d->details['features']);
        Mail::assertQueued(DevisSubmitted::class);
    }

    public function test_unknown_detail_keys_are_dropped(): void
    {
        Mail::fake();

        $this->postJson('/api/devis', $this->payload(['details' => ['site_kind' => 'Nouveau site', 'hack' => 'x']]))->assertOk();

        $this->assertSame(['site_kind' => 'Nouveau site'], Devis::firstOrFail()->details);
    }

    public function test_values_outside_the_allowed_options_are_rejected(): void
    {
        $this->postJson('/api/devis', $this->payload(['details' => ['site_kind' => 'Autre chose']]))
            ->assertStatus(422)->assertJsonValidationErrors('details.site_kind');
        $this->postJson('/api/devis', $this->payload(['details' => ['features' => ['Blog', 'Inconnu']]]))
            ->assertStatus(422)->assertJsonValidationErrors('details.features.1');
        $this->postJson('/api/devis', $this->payload(['deadline' => 'hier']))->assertStatus(422)->assertJsonValidationErrors('deadline');
        $this->postJson('/api/devis', $this->payload(['source' => 'http://evil.test']))->assertStatus(422)->assertJsonValidationErrors('source');
        $this->postJson('/api/devis', $this->payload(['details' => ['existing_url' => 'javascript:alert(1)']]))
            ->assertStatus(422)->assertJsonValidationErrors('details.existing_url');
    }

    public function test_description_is_required_for_other_and_unguided_types(): void
    {
        $this->postJson('/api/devis', $this->payload(['project_type' => 'Autre']))->assertStatus(422)->assertJsonValidationErrors('description');
        $this->postJson('/api/devis', $this->payload(['project_type' => 'Demande rapide (panneau latéral)']))->assertStatus(422);
    }

    public function test_details_are_ignored_for_unguided_types(): void
    {
        Mail::fake();

        $this->postJson('/api/devis', $this->payload(['project_type' => 'Autre', 'description' => 'Besoin', 'details' => ['x' => 'y']]))->assertOk();

        $this->assertNull(Devis::firstOrFail()->details);
    }

    public function test_admin_resource_and_email_show_readable_details(): void
    {
        $d = Devis::create($this->payload([
            'details' => ['site_kind' => 'Nouveau site', 'features' => ['Blog', 'Multilingue']],
        ]));

        $this->assertSame(
            [['label' => 'Nouveau site ou refonte', 'value' => 'Nouveau site'], ['label' => 'Fonctionnalités', 'value' => 'Blog, Multilingue']],
            $d->detailsList()
        );

        $html = (new DevisSubmitted($d))->build()->render();
        $this->assertStringContainsString('Fonctionnalités', $html);
        $this->assertStringContainsString('Blog, Multilingue', $html);
    }
}
