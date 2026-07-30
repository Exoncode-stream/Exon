<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role): array
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'user_art_' . $role,
            'password' => 'password123',
            'role' => $role,
            'token' => hash('sha256', $plainToken),
        ]);

        return [$user, $plainToken];
    }

    public function test_public_can_get_articles_list(): void
    {
        Article::create(['title' => 'Art 1', 'content' => 'Content 1']);
        Article::create(['title' => 'Art 2', 'content' => 'Content 2']);

        $response = $this->getJson('/api/articles');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_authenticated_admin_can_create_article(): void
    {
        [$user, $token] = $this->createUserWithRole('admin');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/articles', [
                'title' => 'Mon premier article',
                'content' => 'Voici le contenu de mon premier article sur Exon.',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Article ajouté avec succès !']);

        $this->assertDatabaseHas('articles', [
            'title' => 'Mon premier article',
        ]);
    }

    public function test_viewer_cannot_create_article(): void
    {
        [$user, $token] = $this->createUserWithRole('viewer');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/articles', [
                'title' => 'Article non autorisé',
                'content' => 'Ceci ne devrait pas être publié par un viewer.',
            ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_cannot_create_article(): void
    {
        $response = $this->postJson('/api/articles', [
            'title' => 'Article anonyme',
            'content' => 'Ceci ne devrait pas passer.',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_update_article(): void
    {
        [$admin, $token] = $this->createUserWithRole('admin');
        $article = Article::create(['title' => 'Old Title', 'content' => 'Old Content']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/articles/' . $article->id, [
                'title' => 'New Title',
                'content' => 'New Content',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Article mis à jour avec succès !']);

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'title' => 'New Title',
            'content' => 'New Content',
        ]);
    }

    public function test_admin_can_delete_article(): void
    {
        [$admin, $token] = $this->createUserWithRole('admin');
        $article = Article::create(['title' => 'To Delete', 'content' => 'Delete Me']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/articles/' . $article->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Article supprimé avec succès !']);

        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_viewer_cannot_delete_article(): void
    {
        [$viewer, $token] = $this->createUserWithRole('viewer');
        $article = Article::create(['title' => 'Protected', 'content' => 'Protected Content']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/articles/' . $article->id);

        $response->assertStatus(403);
    }
}
