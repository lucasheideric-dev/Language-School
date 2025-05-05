<?php

namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestCase;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class AuthControllerTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.secretKey', 'test_secret_key');
    }

    public function testLoginValidCredentials()
    {
        $userTable = TableRegistry::getTableLocator()->get('Users');
        $user = $userTable->newEntity([
            'username' => 'testuser',
            'email' => 'admin@gmail.com',
            'password' => '123456',
        ]);
        $userTable->save($user);

        $this->configRequest([
            'headers' => ['Content-Type' => 'application/json'],
            'input' => json_encode([
                'email' => 'admin@gmail.com',
                'password' => '123456',
            ])
        ]);
        $this->post('/api/auth/login');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"token":');
        $this->assertResponseContains('"user":');
    }

    public function testLoginInvalidCredentials()
    {
        $this->configRequest([
            'headers' => ['Content-Type' => 'application/json'],
            'input' => json_encode([
                'email' => 'invalid@gmail.com',
                'password' => 'wrongpassword',
            ])
        ]);
        $this->post('/api/auth/login');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Usuário ou senha inválidos."');
    }

    public function testLoginMissingFields()
    {
        $this->configRequest([
            'headers' => ['Content-Type' => 'application/json'],
            'input' => json_encode([
                'email' => 'admin@gmail.com',
            ])
        ]);
        $this->post('/api/auth/login');

        $this->assertResponseCode(400);
        $this->assertResponseContains('"error": "Campos obrigatórios não fornecidos."');
    }

    public function testLogoutValidToken()
    {
        $userTable = TableRegistry::getTableLocator()->get('Users');
        $user = $userTable->newEntity([
            'username' => 'testuser',
            'email' => 'admin@gmail.com',
            'password' => '123456',
        ]);
        $userTable->save($user);

        $payload = [
            'sub' => $user->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->post('/api/auth/logout');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"success": "Logout realizado com sucesso."');
    }

    public function testLogoutInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->post('/api/auth/logout');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testLogoutNoToken()
    {
        $this->post('/api/auth/logout');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }
}
