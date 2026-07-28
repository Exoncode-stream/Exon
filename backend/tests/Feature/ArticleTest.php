<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_article(): void
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'writer',
            'password' => 'password123',
            'role' => 'sub',
            'token' => hash('sha256', $plainToken),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->postJson('/api/articles', [
                'title' => 'Mon premier article',
                'content' => 'Voici le contenu de mon premier article sur Exon.',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Article added successfully!']);

        $this->assertDatabaseHas('articles', [
            'title' => 'Mon premier article',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_article(): void
    {
        $response = $this->postJson('/api/articles', [
            'title' => 'Article anonyme',
            'content' => 'Ceci ne devrait pas passer.',
        ]);

        $response->assertStatus(401);
    }
}
