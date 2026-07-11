<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use RefreshDatabase;

    private function createAuthenticatedUser(string $role = 'admin'): User
    {
        return User::create([
            'username' => 'testadmin',
            'password' => 'password',
            'role' => $role,
            'token' => 'test-token-video-' . $role,
        ]);
    }

    // ==========================================
    // ADD VIDEO TESTS
    // ==========================================

    public function test_add_video_missing_token(): void
    {
        $response = $this->postJson('/api/videos', [
            'title' => 'Test Video',
            'youtube_id' => 'testid',
            'category' => 'Test Category',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized - Token missing']);
    }

    public function test_add_video_invalid_token(): void
    {
        $response = $this->postJson('/api/videos', [
            'title' => 'Test Video',
            'youtube_id' => 'testid',
            'category' => 'Test Category',
        ], [
            'Authorization' => 'Bearer invalidtoken123',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Unauthorized - Invalid token']);
    }

    public function test_add_video_missing_fields(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/videos', [
            'title' => 'Test Video',
        ], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(422);
    }

    public function test_add_video_success(): void
    {
        $user = $this->createAuthenticatedUser();

        $response = $this->postJson('/api/videos', [
            'title' => 'PHPUnit Test Video',
            'youtube_id' => 'phpunit123',
            'category' => 'Testing',
        ], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Video added successfully!']);

        $this->assertDatabaseHas('videos', ['title' => 'PHPUnit Test Video']);
    }

    // ==========================================
    // DELETE VIDEO TESTS
    // ==========================================

    public function test_delete_video_missing_token(): void
    {
        $response = $this->deleteJson('/api/videos/1');

        $response->assertStatus(401);
    }

    public function test_delete_video_forbidden_for_viewer(): void
    {
        $user = $this->createAuthenticatedUser('viewer');

        $response = $this->deleteJson('/api/videos/1', [], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(403);
    }

    public function test_delete_video_not_found(): void
    {
        $user = $this->createAuthenticatedUser('admin');

        $response = $this->deleteJson('/api/videos/999999', [], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(404)
            ->assertJson(['error' => 'Video not found']);
    }

    public function test_delete_video_success_as_admin(): void
    {
        $user = $this->createAuthenticatedUser('admin');
        $video = Video::create([
            'title' => 'Delete Me',
            'youtube_id' => 'deleteme123',
            'category' => 'Test',
        ]);

        $response = $this->deleteJson('/api/videos/' . $video->id, [], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Video deleted successfully']);

        $this->assertDatabaseMissing('videos', ['id' => $video->id]);
    }

    public function test_delete_video_success_as_moderator(): void
    {
        $user = $this->createAuthenticatedUser('moderator');
        $video = Video::create([
            'title' => 'Delete Me',
            'youtube_id' => 'deleteme456',
            'category' => 'Test',
        ]);

        $response = $this->deleteJson('/api/videos/' . $video->id, [], [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(200);
    }
}
