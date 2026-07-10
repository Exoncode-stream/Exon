<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ApiTest extends TestCase
{
    private $client;
    private $baseUrl = 'http://backend/';

    protected function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        $db = new \PDO('sqlite:' . __DIR__ . '/../database.sqlite');
        $db->exec("DELETE FROM videos WHERE title LIKE 'PHPUnit Test%'");
        $db->exec("DELETE FROM articles WHERE title LIKE 'PHPUnit Test%'");
        $db->exec("DELETE FROM users WHERE username = 'testuser'");
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
        $this->assertArrayHasKey('role', $data);
        $this->assertArrayHasKey('username', $data);
        $this->assertEquals('Login successful', $data['message']);

        return $data['token'];
    }

    public function testRegisterMissingCredentials()
    {
        $response = $this->client->post('register.php', [
            'json' => ['username' => 'ab']
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testRegisterTooShortUsername()
    {
        $response = $this->client->post('register.php', [
            'json' => ['username' => 'ab', 'password' => 'testpass']
        ]);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertStringContainsString('at least', $data['error']);
    }

    public function testRegisterTooShortPassword()
    {
        $response = $this->client->post('register.php', [
            'json' => ['username' => 'testuser', 'password' => '1234']
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testRegisterSuccess()
    {
        $response = $this->client->post('register.php', [
            'json' => ['username' => 'testuser', 'password' => 'testpass']
        ]);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['success']);
    }

    public function testRegisterDuplicateUsername()
    {
        $response = $this->client->post('register.php', [
            'json' => ['username' => 'testuser', 'password' => 'testpass']
        ]);
        $this->assertEquals(409, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Username already exists', $data['error']);
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

    public function testVerifyTokenMissingToken()
    {
        $response = $this->client->get('verify-token.php');
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testVerifyTokenInvalidToken()
    {
        $response = $this->client->get('verify-token.php', [
            'headers' => [
                'Authorization' => 'Bearer invalidtoken'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testVerifyTokenSuccess($token)
    {
        $response = $this->client->get('verify-token.php', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ]
        ]);
        $this->assertEquals(200, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertTrue($data['valid']);
        $this->assertArrayHasKey('role', $data);
    }

    public function testAddArticleMethodNotAllowed()
    {
        $response = $this->client->get('add-article.php');
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testAddArticleMissingToken()
    {
        $response = $this->client->post('add-article.php', [
            'json' => [
                'title' => 'Test Article',
                'content' => 'Test Content'
            ]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testAddArticleMissingFields($token)
    {
        $response = $this->client->post('add-article.php', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'title' => 'Test Article'
            ]
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testAddArticleSuccess($token)
    {
        $response = $this->client->post('add-article.php', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token
            ],
            'json' => [
                'title' => 'PHPUnit Test Article',
                'content' => '<strong>Test</strong> content'
            ]
        ]);
        $this->assertEquals(201, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertEquals('Article added successfully!', $data['message']);
    }

    public function testDeleteVideoMethodNotAllowed()
    {
        $response = $this->client->get('delete-video.php');
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testDeleteVideoMissingToken()
    {
        $response = $this->client->post('delete-video.php', [
            'json' => ['id' => 999]
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testDeleteVideoNotFound($token)
    {
        $response = $this->client->post('delete-video.php', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => ['id' => 999999]
        ]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testDeleteVideoMissingId($token)
    {
        $response = $this->client->post('delete-video.php', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => []
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testListUsersMethodNotAllowed()
    {
        $response = $this->client->post('list-users.php');
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testListUsersMissingToken()
    {
        $response = $this->client->get('list-users.php');
        $this->assertEquals(401, $response->getStatusCode());
    }

    public function testListUsersInvalidToken()
    {
        $response = $this->client->get('list-users.php', [
            'headers' => ['Authorization' => 'Bearer invalidtoken']
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testListUsersSuccess($token)
    {
        $response = $this->client->get('list-users.php', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->assertEquals(200, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertArrayHasKey('users', $data);
        $this->assertIsArray($data['users']);
        $this->assertGreaterThan(0, count($data['users']));

        $user = $data['users'][0];
        $this->assertArrayHasKey('id', $user);
        $this->assertArrayHasKey('username', $user);
        $this->assertArrayHasKey('role', $user);
    }

    public function testUpdateRoleMethodNotAllowed()
    {
        $response = $this->client->get('update-role.php');
        $this->assertEquals(405, $response->getStatusCode());
    }

    public function testUpdateRoleMissingToken()
    {
        $response = $this->client->post('update-role.php', [
            'json' => ['user_id' => 1, 'role' => 'moderator']
        ]);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testUpdateRoleMissingFields($token)
    {
        $response = $this->client->post('update-role.php', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => ['user_id' => 1]
        ]);
        $this->assertEquals(400, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testUpdateRoleInvalidRole($token)
    {
        $response = $this->client->post('update-role.php', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => ['user_id' => 1, 'role' => 'superadmin']
        ]);
        $this->assertEquals(400, $response->getStatusCode());

        $data = json_decode($response->getBody(), true);
        $this->assertStringContainsString('Invalid role', $data['error']);
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testUpdateRoleUserNotFound($token)
    {
        $response = $this->client->post('update-role.php', [
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'json' => ['user_id' => 999999, 'role' => 'moderator']
        ]);
        $this->assertEquals(404, $response->getStatusCode());
    }

    /**
     * @depends testLoginSuccessAndReturnsToken
     */
    public function testUpdateRoleSuccess($token)
    {
        $this->client->post('register.php', [
            'json' => ['username' => 'testuser', 'password' => 'testpass']
        ]);

        $listResp = $this->client->get('list-users.php', [
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $users = json_decode($listResp->getBody(), true)['users'];
        $testUserId = null;
        foreach ($users as $u) {
            if ($u['username'] === 'testuser') {
                $testUserId = $u['id'];
                break;
            }
        }

        if ($testUserId) {
            $response = $this->client->post('update-role.php', [
                'headers' => ['Authorization' => 'Bearer ' . $token],
                'json' => ['user_id' => $testUserId, 'role' => 'moderator']
            ]);
            $this->assertEquals(200, $response->getStatusCode());

            $data = json_decode($response->getBody(), true);
            $this->assertEquals('Role updated successfully', $data['message']);
        } else {
            $this->markTestSkipped('Test user not found');
        }
    }
}
