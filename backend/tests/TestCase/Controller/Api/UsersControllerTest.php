<?php

namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestCase;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class UsersControllerTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.secretKey', 'test_secret_key');
    }

    public function testViewValidToken()
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
        $this->get('/api/users/view/' . $user->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"user":');
        $this->assertResponseContains('"username": "testuser"');
    }

    public function testViewInvalidToken()
    {
        $invalidToken = 'Bearer invalid_token';

        $this->configRequest([
            'headers' => ['Authorization' => $invalidToken]
        ]);
        $this->get('/api/users/view/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testViewUserNotFound()
    {
        $userTable = TableRegistry::getTableLocator()->get('Users');
        $user = $userTable->newEntity([
            'username' => 'testuser',
            'email' => 'testuser@example.com',
            'password' => 'password123',
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
        $this->get('/api/users/view/9999');

        $this->assertResponseCode(404);
        $this->assertResponseContains('"error": "Usuário não encontrado."');
    }

    public function testIndexValidToken()
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
        $this->get('/api/users/index');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"users":');
    }

    public function testIndexInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/users/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testIndexNoToken()
    {
        $this->get('/api/users/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testAddValidToken()
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
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'input' => json_encode([
                'username' => 'newuser',
                'email' => 'newuser@gmail.com',
                'password' => '123456',
            ])
        ]);
        $this->post('/api/users/add');

        $this->assertResponseCode(201);
        $this->assertResponseContains('"success": "Usuário cadastrado com sucesso."');
    }

    public function testAddInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token'],
            'input' => json_encode([
                'username' => 'newuser',
                'email' => 'newuser@gmail.com',
                'password' => '123456',
            ])
        ]);
        $this->post('/api/users/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testAddNoToken()
    {
        $this->configRequest([
            'input' => json_encode([
                'username' => 'newuser',
                'email' => 'newuser@gmail.com',
                'password' => '123456',
            ])
        ]);
        $this->post('/api/users/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testDeleteValidToken()
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
        $this->delete('/api/users/delete/' . $user->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"success": "Usuário deletado com sucesso."');
    }

    public function testDeleteInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->delete('/api/users/delete/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testDeleteNoToken()
    {
        $this->delete('/api/users/delete/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testEditValidToken()
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
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'input' => json_encode([
                'username' => 'updateduser',
                'email' => 'updated@gmail.com',
            ])
        ]);
        $this->put('/api/users/edit/' . $user->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"success": "Usuário atualizado com sucesso."');
    }

    public function testEditInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token'],
            'input' => json_encode([
                'username' => 'updateduser',
                'email' => 'updated@gmail.com',
            ])
        ]);
        $this->put('/api/users/edit/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testEditNoToken()
    {
        $this->configRequest([
            'input' => json_encode([
                'username' => 'updateduser',
                'email' => 'updated@gmail.com',
            ])
        ]);
        $this->put('/api/users/edit/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testEditUserNotFound()
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
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'input' => json_encode([
                'username' => 'updateduser',
                'email' => 'updated@gmail.com',
            ])
        ]);
        $this->put('/api/users/edit/9999');

        $this->assertResponseCode(404);
        $this->assertResponseContains('"error": "Usuário não encontrado."');
    }

    public function testListValidToken()
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
        $this->get('/api/users/list');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"users":');
    }

    public function testListInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/users/list');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testListNoToken()
    {
        $this->get('/api/users/list');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }
}
