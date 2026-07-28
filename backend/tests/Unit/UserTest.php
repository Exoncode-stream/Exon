<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_password_is_automatically_hashed(): void
    {
        $user = User::create([
            'username' => 'testuser',
            'password' => 'secret123',
            'role' => 'viewer',
        ]);

        $this->assertNotEquals('secret123', $user->password);
        $this->assertTrue(Hash::check('secret123', $user->password));
    }

    public function test_user_default_role_is_viewer(): void
    {
        $user = User::create([
            'username' => 'defaultrole',
            'password' => 'password123',
        ]);

        $this->assertEquals('viewer', $user->role);
    }
}
