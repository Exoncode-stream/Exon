<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $username, string $password = 'password123'): array
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => $username,
            'password' => Hash::make($password),
            'role' => 'viewer',
            'token' => hash('sha256', $plainToken),
        ]);

        return [$user, $plainToken];
    }

    public function test_authenticated_user_can_fetch_own_profile(): void
    {
        [$user, $token] = $this->createUser('profileuser');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/profile');

        $response->assertStatus(200)
            ->assertJson([
                'username' => 'profileuser',
                'role' => 'viewer',
                'stats' => [
                    'comments_count' => 0,
                    'likes_count' => 0,
                ],
            ]);
    }

    public function test_user_can_change_password_with_valid_current_password(): void
    {
        [$user, $token] = $this->createUser('pwduser', 'oldsecret123');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile/password', [
                'current_password' => 'oldsecret123',
                'new_password' => 'newsecret123',
                'new_password_confirmation' => 'newsecret123',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Mot de passe mis à jour avec succès !']);

        $user->refresh();
        $this->assertTrue(Hash::check('newsecret123', $user->password));
    }

    public function test_password_change_fails_with_invalid_current_password(): void
    {
        [$user, $token] = $this->createUser('pwduser', 'realpassword');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile/password', [
                'current_password' => 'wrongpassword',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ]);

        $response->assertStatus(422)
            ->assertJson(['error' => 'Mot de passe actuel incorrect.']);
    }

    public function test_password_change_fails_with_short_new_password(): void
    {
        [$user, $token] = $this->createUser('pwduser', 'realpassword');

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/profile/password', [
                'current_password' => 'realpassword',
                'new_password' => 'short',
                'new_password_confirmation' => 'short',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['new_password']);
    }

    public function test_unauthenticated_user_cannot_access_profile(): void
    {
        $response = $this->getJson('/api/profile');
        $response->assertStatus(401);
    }
}
