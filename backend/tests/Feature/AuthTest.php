<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_with_valid_data(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'newuser',
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Inscription réussie ! Vous pouvez maintenant vous connecter.',
            ]);

        $this->assertDatabaseHas('users', [
            'username' => 'newuser',
            'role' => 'viewer',
        ]);
    }

    public function test_registration_fails_with_short_password(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'validuser',
            'password' => '12345', // min:8 required
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_registration_fails_with_invalid_username_characters(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'user@invalid!',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_registration_fails_with_duplicate_username(): void
    {
        User::create([
            'username' => 'existinguser',
            'password' => 'password123',
        ]);

        $response = $this->postJson('/api/register', [
            'username' => 'existinguser',
            'password' => 'password123',
        ]);

        $response->assertStatus(409)
            ->assertJson(['error' => 'Ce nom d\'utilisateur existe déjà.']);
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::create([
            'username' => 'john_doe',
            'password' => 'securepass123',
            'role' => 'viewer',
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'john_doe',
            'password' => 'securepass123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'username' => 'john_doe',
                'role' => 'viewer',
            ])
            ->assertCookie('exon_token');

        $this->assertNotNull($response->json('token'));
        $user->refresh();
        $this->assertNotNull($user->token_expires_at);
        $this->assertTrue($user->token_expires_at->isFuture());
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::create([
            'username' => 'john_doe',
            'password' => 'securepass123',
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'john_doe',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Identifiants invalides']);
    }

    public function test_user_can_verify_valid_token(): void
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'activeuser',
            'password' => 'password123',
            'token' => hash('sha256', $plainToken),
            'role' => 'admin',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->getJson('/api/verify-token');

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'username' => 'activeuser',
                'role' => 'admin',
            ]);
    }

    public function test_user_can_verify_valid_token_via_cookie(): void
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'cookieuser',
            'password' => 'password123',
            'token' => hash('sha256', $plainToken),
            'role' => 'moderator',
        ]);

        $response = $this->call('GET', '/api/verify-token', [], ['exon_token' => $plainToken]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'username' => 'cookieuser',
                'role' => 'moderator',
            ]);
    }

    public function test_verify_token_fails_with_invalid_token(): void
    {
        $response = $this->withHeader('Authorization', 'Bearer invalid_token_value')
            ->getJson('/api/verify-token');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Non autorisé - Token invalide']);
    }

    public function test_verify_token_fails_with_expired_token(): void
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'expireduser',
            'password' => 'password123',
            'token' => hash('sha256', $plainToken),
            'token_expires_at' => now()->subDay(),
            'role' => 'viewer',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->getJson('/api/verify-token');

        $response->assertStatus(401)
            ->assertJson(['error' => 'Non autorisé - Token expiré']);
    }

    public function test_user_can_logout_and_invalidate_token(): void
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'logoutuser',
            'password' => 'password123',
            'token' => hash('sha256', $plainToken),
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $plainToken)
            ->postJson('/api/logout');

        $response->assertStatus(200)
            ->assertJson(['message' => 'Déconnexion réussie.']);

        $user->refresh();
        $this->assertNull($user->token);
        $this->assertNull($user->token_expires_at);
    }
}

