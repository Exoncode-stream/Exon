<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    // ==========================================
    // LOGIN TESTS
    // ==========================================

    public function test_login_missing_credentials(): void
    {
        $response = $this->postJson('/api/login', ['username' => 'admin']);

        $response->assertStatus(422);
    }

    public function test_login_invalid_credentials(): void
    {
        User::create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'admin',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['error' => 'Invalid credentials']);
    }

    public function test_login_success_and_returns_token(): void
    {
        User::create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin',
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Login successful',
            ])
            ->assertJsonStructure(['token', 'username', 'role']);
    }

    // ==========================================
    // REGISTER TESTS
    // ==========================================

    public function test_register_missing_credentials(): void
    {
        $response = $this->postJson('/api/register', ['username' => 'ab']);

        $response->assertStatus(422);
    }

    public function test_register_too_short_username(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'ab',
            'password' => 'testpass',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_too_short_password(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'password' => '1234',
        ]);

        $response->assertStatus(422);
    }

    public function test_register_success(): void
    {
        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'username' => 'testuser',
            'role' => 'viewer',
        ]);
    }

    public function test_register_duplicate_username(): void
    {
        User::create([
            'username' => 'testuser',
            'password' => 'testpass',
            'role' => 'viewer',
        ]);

        $response = $this->postJson('/api/register', [
            'username' => 'testuser',
            'password' => 'testpass',
        ]);

        $response->assertStatus(409)
            ->assertJson(['error' => 'Username already exists']);
    }

    // ==========================================
    // VERIFY TOKEN TESTS
    // ==========================================

    public function test_verify_token_missing_token(): void
    {
        $response = $this->getJson('/api/verify-token');

        $response->assertStatus(401);
    }

    public function test_verify_token_invalid_token(): void
    {
        $response = $this->getJson('/api/verify-token', [
            'Authorization' => 'Bearer invalidtoken',
        ]);

        $response->assertStatus(401);
    }

    public function test_verify_token_success(): void
    {
        $user = User::create([
            'username' => 'admin',
            'password' => 'admin',
            'role' => 'admin',
            'token' => 'valid-test-token-123',
        ]);

        $response = $this->getJson('/api/verify-token', [
            'Authorization' => 'Bearer valid-test-token-123',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'valid' => true,
                'username' => 'admin',
                'role' => 'admin',
            ]);
    }
}
