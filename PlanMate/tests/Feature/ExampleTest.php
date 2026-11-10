<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/dashboard');

        $pages = ['/login', '/register', '/dashboard', '/aktivitas', '/goals', '/kategori', '/profil', '/kalender', '/deadline', '/pencarian'];
        foreach ($pages as $page) {
            $res = $this->get($page);
            $res->assertStatus(200);
        }
    }
}
