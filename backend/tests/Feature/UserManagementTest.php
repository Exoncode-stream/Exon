<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createAdmin(): User
    {
        return User::create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin',
            'token' => 'admin-test-token',
        ]);
    }

    // ==========================================
    // LIST USERS TESTS
    // ==========================================

    public function test_list_users_missing_token(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(401);
    }

    public function test_list_users_invalid_token(): void
    {
        $response = $this->getJson('/api/users', [
            'Authorization' => 'Bearer invalidtoken',
        ]);

        $response->assertStatus(401);
    }

    public function test_list_users_forbidden_for_non_admin(): void
    {
        $user = User::create([
            'username' => 'viewer',
            'password' => 'password',
            'role' => 'viewer',
            'token' => 'viewer-test-token',
        ]);

        $response = $this->getJson('/api/users', [
            'Authorization' => 'Bearer ' . $user->token,
        ]);

        $response->assertStatus(403);
    }

    public function test_list_users_success(): void
    {
        $admin = $this->createAdmin();

        $response = $this->getJson('/api/users', [
            'Authorization' => 'Bearer ' . $admin->token,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'users' => [
                    '*' => ['id', 'username', 'role'],
                ],
            ]);
    }

    // ==========================================
    // UPDATE ROLE TESTS
    // ==========================================

    public function test_update_role_missing_token(): void
    {
        $response = $this->putJson('/api/users/1/role', [
            'role' => 'moderator',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_role_missing_fields(): void
    {
        $admin = $this->createAdmin();

        $response = $this->putJson('/api/users/1/role', [], [
            'Authorization' => 'Bearer ' . $admin->token,
        ]);

        $response->assertStatus(422);
    }

    public function test_update_role_invalid_role(): void
    {
        $admin = $this->createAdmin();

        $response = $this->putJson('/api/users/' . $admin->id . '/role', [
            'role' => 'superadmin',
        ], [
            'Authorization' => 'Bearer ' . $admin->token,
        ]);

        $response->assertStatus(400)
            ->assertJsonFragment(['error' => 'Invalid role. Allowed: viewer, sub, moderator, admin']);
    }

    public function test_update_role_user_not_found(): void
    {
        $admin = $this->createAdmin();

        $response = $this->putJson('/api/users/999999/role', [
            'role' => 'moderator',
        ], [
            'Authorization' => 'Bearer ' . $admin->token,
        ]);

        $response->assertStatus(404);
    }

    public function test_update_role_success(): void
    {
        $admin = $this->createAdmin();

        $targetUser = User::create([
            'username' => 'testuser',
            'password' => 'testpass',
            'role' => 'viewer',
        ]);

        $response = $this->putJson('/api/users/' . $targetUser->id . '/role', [
            'role' => 'moderator',
        ], [
            'Authorization' => 'Bearer ' . $admin->token,
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Role updated successfully']);

        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => 'moderator',
        ]);
    }
}
