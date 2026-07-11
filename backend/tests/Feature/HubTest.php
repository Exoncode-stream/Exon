<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Video;
use App\Models\Article;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubTest extends TestCase
{
    use RefreshDatabase;

    public function test_hub_returns_data(): void
    {
        // Seed some data
        Link::create(['name' => 'YouTube', 'url' => 'https://youtube.com']);
        Video::create(['title' => 'Test Video', 'youtube_id' => 'abc123', 'category' => 'Test']);
        Article::create(['title' => 'Test Article', 'content' => 'Test content']);

        $response = $this->getJson('/api/hub');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'pseudo',
                'description',
                'linksHtml',
                'videosHtml',
                'articles',
            ]);
    }

    public function test_hub_returns_empty_when_no_data(): void
    {
        $response = $this->getJson('/api/hub');

        $response->assertStatus(200)
            ->assertJson([
                'pseudo' => 'Exon',
                'linksHtml' => '',
                'videosHtml' => '',
                'articles' => [],
            ]);
    }
}
