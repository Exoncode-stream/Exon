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
            'username' => 'user_vid_' . $role,
            'password' => 'password123',
            'role' => $role,
            'token' => hash('sha256', $plainToken),
        ]);

        return [$user, $plainToken];
    }

    public function test_public_can_get_videos_list(): void
    {
        Video::create(['title' => 'V1', 'youtube_id' => 'yt1', 'category' => 'Cat1']);
        Video::create(['title' => 'V2', 'youtube_id' => 'yt2', 'category' => 'Cat2']);

        $response = $this->getJson('/api/videos');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_authenticated_admin_can_create_video(): void
    {
        [$user, $token] = $this->createUserWithRole('admin');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/videos', [
                'title' => 'Laravel 12 Tutorial',
                'youtube_id' => 'dQw4w9WgXcQ',
                'category' => 'Laravel',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Vidéo ajoutée avec succès !']);

        $this->assertDatabaseHas('videos', [
            'title' => 'Laravel 12 Tutorial',
            'youtube_id' => 'dQw4w9WgXcQ',
        ]);
    }

    public function test_viewer_cannot_create_video(): void
    {
        [$user, $token] = $this->createUserWithRole('viewer');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/videos', [
                'title' => 'Vidéo non autorisée',
                'youtube_id' => 'abc123xyz',
                'category' => 'Test',
            ]);

        $response->assertStatus(403);
    }

    public function test_admin_can_update_video(): void
    {
        [$admin, $token] = $this->createUserWithRole('admin');
        $video = Video::create(['title' => 'Old Title', 'youtube_id' => 'old_id', 'category' => 'Old Cat']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/videos/' . $video->id, [
                'title' => 'New Title',
                'youtube_id' => 'new_id',
                'category' => 'New Cat',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Vidéo mise à jour avec succès !']);

        $this->assertDatabaseHas('videos', [
            'id' => $video->id,
            'title' => 'New Title',
            'youtube_id' => 'new_id',
            'category' => 'New Cat',
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
            ->assertJson(['message' => 'Vidéo supprimée avec succès']);

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
