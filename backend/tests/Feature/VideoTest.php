<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role): array
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'user_' . $role,
            'password' => 'password123',
            'role' => $role,
            'token' => hash('sha256', $plainToken),
        ]);

        return [$user, $plainToken];
    }

    public function test_authenticated_user_can_create_video(): void
    {
        [$user, $token] = $this->createUserWithRole('viewer');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/videos', [
                'title' => 'Laravel 12 Tutorial',
                'youtube_id' => 'dQw4w9WgXcQ',
                'category' => 'Laravel',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Video added successfully!']);

        $this->assertDatabaseHas('videos', [
            'title' => 'Laravel 12 Tutorial',
            'youtube_id' => 'dQw4w9WgXcQ',
        ]);
    }

    public function test_admin_can_delete_video(): void
    {
        [$user, $token] = $this->createUserWithRole('admin');
        $video = Video::create([
            'title' => 'Delete Me',
            'youtube_id' => '12345',
            'category' => 'Test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/videos/' . $video->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Video deleted successfully']);

        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }

    public function test_viewer_cannot_delete_video(): void
    {
        [$user, $token] = $this->createUserWithRole('viewer');
        $video = Video::create([
            'title' => 'Protected Video',
            'youtube_id' => '12345',
            'category' => 'Test',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/videos/' . $video->id);

        $response->assertStatus(403);
    }
}
