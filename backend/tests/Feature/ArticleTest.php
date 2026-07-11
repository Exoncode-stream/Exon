<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(): User
    {
        return User::create([
            'username' => 'testadmin',
            'password' => 'password',
            'role' => 'admin',
            'token' => 'test-token-article',
        ]);
    }

    public function test_add_article_missing_token(): void
    {
        $response = $this->postJson('/api/articles', [
            'title' => 'Test Article',
            'content' => 'Test Content',
        ]);

        $response->assertStatus(401);
    }

    public function test_add_article_missing_fields(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/articles', [
            'title' => 'Test Article',
        ], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(422);
    }

    public function test_add_article_success(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/articles', [
            'title' => 'PHPUnit Test Article',
            'content' => '<strong>Test</strong> content',
        ], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Article added successfully!']);

        $this->assertDatabaseHas('articles', ['title' => 'PHPUnit Test Article']);
    }
}
