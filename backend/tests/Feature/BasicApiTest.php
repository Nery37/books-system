<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function autores_api_returns_successful_response()
    {
        $response = $this->getJson('/api/v1/autores');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function assuntos_api_returns_successful_response()
    {
        $response = $this->getJson('/api/v1/assuntos');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function livros_api_returns_successful_response()
    {
        $response = $this->getJson('/api/v1/livros');
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'data']);
        $this->assertTrue($response->json('success'));
    }

    /** @test */
    public function api_returns_404_for_nonexistent_autor()
    {
        $response = $this->getJson('/api/v1/autores/999999');
        $response->assertStatus(404);
    }

    /** @test */
    public function api_returns_404_for_nonexistent_assunto()
    {
        $response = $this->getJson('/api/v1/assuntos/999999');
        $response->assertStatus(404);
    }

    /** @test */
    public function api_returns_404_for_nonexistent_livro()
    {
        $response = $this->getJson('/api/v1/livros/999999');
        $response->assertStatus(404);
    }

    /** @test */
    public function api_validates_required_fields_for_autor_creation()
    {
        $response = $this->postJson('/api/v1/autores', []);
        $response->assertStatus(422);
    }

    /** @test */
    public function api_validates_required_fields_for_assunto_creation()
    {
        $response = $this->postJson('/api/v1/assuntos', []);
        $response->assertStatus(422);
    }

    /** @test */
    public function api_validates_required_fields_for_livro_creation()
    {
        $response = $this->postJson('/api/v1/livros', []);
        $response->assertStatus(422);
    }

    /** @test */
    public function api_routes_are_accessible()
    {
        // Test that all main API routes are accessible
        $routes = [
            '/api/v1/autores',
            '/api/v1/assuntos', 
            '/api/v1/livros'
        ];

        foreach ($routes as $route) {
            $response = $this->getJson($route);
            $this->assertTrue(
                in_array($response->status(), [200, 401, 403]),
                "Route {$route} returned unexpected status: {$response->status()}"
            );
        }
    }
}
