<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_root_redirects_to_the_next_admin(): void
    {
        $this->get('/')->assertRedirect(rtrim(config('app.admin_url'), '/'));
    }
}
