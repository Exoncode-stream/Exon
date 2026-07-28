<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class LinkTest extends TestCase
{
    use RefreshDatabase;

    private function createAdminUser(): array
    {
        $plainToken = Str::random(64);
        $user = User::create([
            'username' => 'admin_link',
            'password' => 'password123',
            'role' => 'admin',
            'token' => hash('sha256', $plainToken),
        ]);

        return [$user, $plainToken];
    }

    public function test_public_can_get_links_list(): void
    {
        Link::create(['name' => 'GitHub', 'url' => 'https://github.com']);
        Link::create(['name' => 'Twitter', 'url' => 'https://twitter.com']);

        $response = $this->getJson('/api/links');

        $response->assertStatus(200)
            ->assertJsonCount(2);
    }

    public function test_admin_can_create_link(): void
    {
        [$user, $token] = $this->createAdminUser();

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/links', [
                'name' => 'Discord',
                'url' => 'https://discord.gg/exon',
            ]);

        $response->assertStatus(201)
            ->assertJson(['message' => 'Lien ajouté avec succès !']);

        $this->assertDatabaseHas('links', [
            'name' => 'Discord',
            'url' => 'https://discord.gg/exon',
        ]);
    }

    public function test_unauthenticated_user_cannot_create_link(): void
    {
        $response = $this->postJson('/api/links', [
            'name' => 'HackerNews',
            'url' => 'https://news.ycombinator.com',
        ]);

        $response->assertStatus(401);
    }

    public function test_admin_can_update_link(): void
    {
        [$user, $token] = $this->createAdminUser();
        $link = Link::create(['name' => 'Old Name', 'url' => 'https://old.com']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson('/api/links/' . $link->id, [
                'name' => 'New Name',
                'url' => 'https://new.com',
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Lien mis à jour avec succès !']);

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'name' => 'New Name',
            'url' => 'https://new.com',
        ]);
    }

    public function test_admin_can_delete_link(): void
    {
        [$user, $token] = $this->createAdminUser();
        $link = Link::create(['name' => 'To Delete', 'url' => 'https://delete.me']);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson('/api/links/' . $link->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Lien supprimé avec succès !']);

        $this->assertDatabaseMissing('links', ['id' => $link->id]);
    }
}
