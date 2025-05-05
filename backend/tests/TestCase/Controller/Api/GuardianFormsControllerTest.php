<?php

namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestCase;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class GuardianFormsControllerTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.secretKey', 'test_secret_key');
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
        $this->get('/api/guardian-forms');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"forms":');
    }

    public function testIndexInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/guardian-forms');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testIndexNoToken()
    {
        $this->get('/api/guardian-forms');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
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
        $this->get('/api/guardian-forms/view/' . $user->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"form":');
    }

    public function testViewInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/guardian-forms/view/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
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
                'field1' => 'value1',
                'field2' => 'value2',
            ])
        ]);
        $this->post('/api/guardian-forms/add');

        $this->assertResponseCode(201);
        $this->assertResponseContains('"success": "Formulário cadastrado com sucesso."');
    }

    public function testDeleteValidToken()
    {
        $guardianFormsTable = TableRegistry::getTableLocator()->get('GuardianForms');
        $form = $guardianFormsTable->newEntity([
            'lesson_id' => 1,
            'signed' => false,
            'pdf_path' => 'path/to/pdf',
        ]);
        $guardianFormsTable->save($form);

        $payload = [
            'sub' => 1,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->delete('/api/guardian-forms/delete/' . $form->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"success": "Formulário deletado com sucesso."');
    }

    public function testEditValidToken()
    {
        $guardianFormsTable = TableRegistry::getTableLocator()->get('GuardianForms');
        $form = $guardianFormsTable->newEntity([
            'lesson_id' => 1,
            'signed' => false,
            'pdf_path' => 'path/to/pdf',
        ]);
        $guardianFormsTable->save($form);

        $payload = [
            'sub' => 1,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'input' => json_encode([
                'lesson_id' => 2,
                'signed' => true,
                'pdf_path' => 'path/to/updated/pdf',
            ])
        ]);
        $this->put('/api/guardian-forms/edit/' . $form->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"success": "Formulário atualizado com sucesso."');
    }

    public function testAddInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token'],
            'input' => json_encode([
                'field1' => 'value1',
                'field2' => 'value2',
            ])
        ]);
        $this->post('/api/guardian-forms/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testAddNoToken()
    {
        $this->configRequest([
            'input' => json_encode([
                'field1' => 'value1',
                'field2' => 'value2',
            ])
        ]);
        $this->post('/api/guardian-forms/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }
}
