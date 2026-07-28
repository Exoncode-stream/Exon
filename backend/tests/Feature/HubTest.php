<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Link;
use App\Models\Video;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HubTest extends TestCase
{
    use RefreshDatabase;

    public function test_hub_returns_public_data(): void
    {
        Link::create(['name' => 'GitHub', 'url' => 'https://github.com']);
        Video::create(['title' => 'Sample Video', 'youtube_id' => 'abc1234', 'category' => 'Demo']);
        Article::create(['title' => 'Sample Article', 'content' => 'Lorem ipsum content']);

        $response = $this->getJson('/api/hub');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'pseudo',
                'description',
                'links',
                'videos',
                'articles',
            ]);
    }
}
