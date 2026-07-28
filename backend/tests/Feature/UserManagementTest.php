<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createUserWithRole(string $role, string $username = 'user'): array
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

    public function test_admin_can_list_users(): void
    {
        [$admin, $token] = $this->createUserWithRole('admin', 'main_admin');
        $this->createUserWithRole('viewer', 'viewer_user');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'users');
    }

    public function test_non_admin_cannot_list_users(): void
    {
        [$user, $token] = $this->createUserWithRole('moderator', 'mod_user');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_update_user_role(): void
    {
        [$admin, $adminToken] = $this->createUserWithRole('admin', 'admin_boss');
        [$targetUser] = $this->createUserWithRole('viewer', 'target_user');

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->putJson('/api/users/' . $targetUser->id . '/role', [
                'role' => 'moderator',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Rôle mis à jour avec succès !']);

        $targetUser->refresh();
        $this->assertEquals('moderator', $targetUser->role);
    }

    public function test_admin_cannot_demote_themselves_if_last_admin(): void
    {
        [$admin, $adminToken] = $this->createUserWithRole('admin', 'only_admin');

        $response = $this->withHeader('Authorization', 'Bearer ' . $adminToken)
            ->putJson('/api/users/' . $admin->id . '/role', [
                'role' => 'viewer',
            ]);

        $response->assertStatus(400)
            ->assertJson([
                'error' => 'Impossible de modifier votre propre rôle car vous êtes le dernier administrateur.',
            ]);
    }
}
