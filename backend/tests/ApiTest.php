<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiTest extends TestCase
{
    private $client;
    private $baseUrl = 'http://backend/'; // Defined in docker-compose network

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
        ]);
    }

    public function testIndexReturnsData()
    {
        $response = $this->client->get('index.php');
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('pseudo', $data);
        $this->assertArrayHasKey('description', $data);
        $this->assertArrayHasKey('linksHtml', $data);
        $this->assertArrayHasKey('videosHtml', $data);
        $this->assertArrayHasKey('articles', $data);
    }

    public function testLoginMissingCredentials()
    {
        $response = $this->client->post('login.php', [
            'json' => ['username' => 'admin']
        ]);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Missing credentials', $data['error']);
    }

    public function testLoginInvalidCredentials()
    {
        $response = $this->client->post('login.php', [
            'json' => [
                'username' => 'admin',
                'password' => 'wrongpassword'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Invalid credentials', $data['error']);
    }

    public function testLoginSuccessAndReturnsToken()
    {
        $response = $this->client->post('login.php', [
            'json' => [
                'username' => 'admin',
                'password' => 'admin'
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['success']);
        $this->assertArrayHasKey('token', $data);
        $this->assertEquals('Login successful', $data['message']);

        return $data['token']; // Pass token to dependent tests
    }

    public function testAddVideoMethodNotAllowed()
    {
        $response = $this->client->get('add-video.php');
        $this->assertEquals(405, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Only POST requests are allowed', $data['error']);
    }

    public function testAddVideoMissingToken()
    {
        $response = $this->client->post('add-video.php', [
            'json' => [
                'title' => 'Test Video',
                'youtube_id' => 'testid',
                'category' => 'Test Category'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Unauthorized - Token missing', $data['error']);
    }

    public function testAddVideoInvalidToken()
    {
        $response = $this->client->post('add-video.php', [
            'headers' => [
                'Authorization' => 'Bearer invalidtoken123'
            ],
            'json' => [
                'title' => 'Test Video',
                'youtube_id' => 'testid',
                'category' => 'Test Category'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Unauthorized - Invalid token', $data['error']);
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testAddVideoMissingFields($token)
    {
        $response = $this->client->post('add-video.php', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'title' => 'Test Video'
                // missing youtube_id and category
            ]
        ]);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('All fields are required', $data['error']);
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testAddVideoSuccess($token)
    {
        $response = $this->client->post('add-video.php', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'title' => 'PHPUnit Test Video',
                'youtube_id' => 'phpunit123',
                'category' => 'Testing'
            ]
        ]);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Video added successfully!', $data['message']);
    }
}
