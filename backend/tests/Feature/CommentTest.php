<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommentTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $username, string $role = 'viewer'): array
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => $username,
            'password' => 'password123',
            'role' => $role,
            'token' => hash('sha256', $plainToken),
        ]);

        return [$user, $plainToken];
    }

    public function test_authenticated_user_can_add_comment_to_article(): void
    {
        [$user, $token] = $this->createUser('commenter');
        $article = Article::create(['title' => 'Test Article', 'content' => 'Content']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/articles/' . $article->id . '/comments', [
                'content' => 'Super article !',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Commentaire ajouté avec succès !']);

        $this->assertDatabaseHas('comments', [
            'user_id' => $user->id,
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
            'content' => 'Super article !',
        ]);
    }

    public function test_unauthenticated_user_cannot_add_comment(): void
    {
        $article = Article::create(['title' => 'Test Article', 'content' => 'Content']);

        $response = $this->postJson('/api/articles/' . $article->id . '/comments', [
            'content' => 'Commentaire anonyme',
        ]);

        $response->assertStatus(401);
    }

    public function test_user_can_delete_own_comment(): void
    {
        [$user, $token] = $this->createUser('owner');
        $article = Article::create(['title' => 'Article', 'content' => 'Content']);
        $comment = Comment::create([
            'user_id' => $user->id,
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
            'content' => 'Mon commentaire',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/comments/' . $comment->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Commentaire supprimé avec succès !']);

        $this->assertDatabaseMissing('comments', ['id' => $comment->id]);
    }

    public function test_user_cannot_delete_other_user_comment(): void
    {
        [$owner] = $this->createUser('owner_user');
        [$otherUser, $otherToken] = $this->createUser('other_user');

        $article = Article::create(['title' => 'Article', 'content' => 'Content']);
        $comment = Comment::create([
            'user_id' => $owner->id,
            'commentable_type' => Article::class,
            'commentable_id' => $article->id,
            'content' => 'Commentaire de l\'auteur',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $otherToken)
            ->deleteJson('/api/comments/' . $comment->id);

        $response->assertStatus(403);
    }
}
