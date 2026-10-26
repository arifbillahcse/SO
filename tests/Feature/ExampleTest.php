<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * Needs RefreshDatabase now that `/` is wired to HomeController,
     * which queries the services/testimonials tables (Phase 5) —
     * without a migrated test database this would fail with
     * "no such table: services" rather than asserting a real 200.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
