<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LikeTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $username): array
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => $username,
            'password' => 'password123',
            'role' => 'viewer',
            'token' => hash('sha256', $plainToken),
        ]);

        return [$user, $plainToken];
    }

    public function test_authenticated_user_can_toggle_like_on_article(): void
    {
        [$user, $token] = $this->createUser('liker');
        $article = Article::create(['title' => 'Article to Like', 'content' => 'Content']);

        // First click -> Like
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/articles/' . $article->id . '/like');

        $response->assertStatus(200)
            ->assertJson([
                'liked' => true,
                'likes_count' => 1,
            ]);

        $this->assertDatabaseHas('likes', [
            'user_id' => $user->id,
            'likeable_type' => Article::class,
            'likeable_id' => $article->id,
        ]);

        // Second click -> Unlike
        $response2 = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/articles/' . $article->id . '/like');

        $response2->assertStatus(200)
            ->assertJson([
                'liked' => false,
                'likes_count' => 0,
            ]);
    }

    public function test_unauthenticated_user_cannot_toggle_like(): void
    {
        $video = Video::create(['title' => 'Video', 'youtube_id' => 'abc', 'category' => 'Demo']);

        $response = $this->postJson('/api/videos/' . $video->id . '/like');

        $response->assertStatus(401);
    }
}
