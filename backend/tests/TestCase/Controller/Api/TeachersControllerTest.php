<?php

namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestCase;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class TeachersControllerTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.secretKey', 'test_secret_key');
    }

    public function testViewValidToken()
    {
        $teachersTable = TableRegistry::getTableLocator()->get('Teachers');
        $teacher = $teachersTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'specialty' => 'Matemática',
            'status' => true,
        ]);
        $teachersTable->save($teacher);

        $payload = [
            'sub' => $teacher->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->get('/api/teachers/view/' . $teacher->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"teacher":');
        $this->assertResponseContains('"first_name": "João"');
    }

    public function testViewInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/teachers/view/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testViewTeacherNotFound()
    {
        $teachersTable = TableRegistry::getTableLocator()->get('Teachers');
        $teacher = $teachersTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'specialty' => 'Matemática',
            'status' => true,
        ]);
        $teachersTable->save($teacher);

        $payload = [
            'sub' => $teacher->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->get('/api/teachers/view/9999');

        $this->assertResponseCode(404);
        $this->assertResponseContains('"error": "Professor não encontrado."');
    }

    public function testIndexValidToken()
    {
        $teachersTable = TableRegistry::getTableLocator()->get('Teachers');
        $teacher = $teachersTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'specialty' => 'Matemática',
            'status' => true,
        ]);
        $teachersTable->save($teacher);

        $payload = [
            'sub' => $teacher->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->get('/api/teachers/index');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"teachers":');
    }

    public function testIndexInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/teachers/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testIndexNoToken()
    {
        $this->get('/api/teachers/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testAddValidToken()
    {
        $teachersTable = TableRegistry::getTableLocator()->get('Teachers');
        $teacher = $teachersTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'specialty' => 'Matemática',
            'status' => true,
        ]);
        $teachersTable->save($teacher);

        $payload = [
            'sub' => $teacher->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'input' => json_encode([
                'cpf' => '987.654.321-00',
                'first_name' => 'Maria',
                'last_name' => 'Oliveira',
                'birth_date' => '1990-01-01',
                'specialty' => 'Física',
                'status' => true,
            ])
        ]);
        $this->post('/api/teachers/add');

        $this->assertResponseCode(201);
        $this->assertResponseContains('"success": "Professor cadastrado com sucesso."');
    }

    public function testAddInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token'],
            'input' => json_encode([
                'cpf' => '987.654.321-00',
                'first_name' => 'Maria',
                'last_name' => 'Oliveira',
                'birth_date' => '1990-01-01',
                'specialty' => 'Física',
                'status' => true,
            ])
        ]);
        $this->post('/api/teachers/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testAddNoToken()
    {
        $this->configRequest([
            'input' => json_encode([
                'cpf' => '987.654.321-00',
                'first_name' => 'Maria',
                'last_name' => 'Oliveira',
                'birth_date' => '1990-01-01',
                'specialty' => 'Física',
                'status' => true,
            ])
        ]);
        $this->post('/api/teachers/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }
}
