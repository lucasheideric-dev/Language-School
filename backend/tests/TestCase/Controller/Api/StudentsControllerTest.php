<?php

namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestCase;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class StudentsControllerTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.secretKey', 'test_secret_key');
    }

    public function testViewValidToken()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $student = $studentsTable->newEntity([
            'cpf' => '123.456.789-00',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birth_date' => '2000-01-01',
            'postal_code' => '12345-678',
            'street' => 'Main St',
            'house_number' => '123',
            'neighborhood' => 'Downtown',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '123456789',
            'whatsapp' => '987654321',
            'email' => 'johndoe@example.com',
        ]);
        $studentsTable->save($student);

        $payload = [
            'sub' => $student->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->get('/api/students/view/' . $student->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"student":');
        $this->assertResponseContains('"first_name": "John"');
    }

    public function testViewInvalidToken()
    {
        $invalidToken = 'Bearer invalid_token';

        $this->configRequest([
            'headers' => ['Authorization' => $invalidToken]
        ]);
        $this->get('/api/students/view/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testViewStudentNotFound()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $student = $studentsTable->newEntity([
            'cpf' => '123.456.789-00',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birth_date' => '2000-01-01',
            'postal_code' => '12345-678',
            'street' => 'Main St',
            'house_number' => '123',
            'neighborhood' => 'Downtown',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '123456789',
            'whatsapp' => '987654321',
            'email' => 'johndoe@example.com',
        ]);
        $studentsTable->save($student);

        $payload = [
            'sub' => $student->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->get('/api/students/view/9999');

        $this->assertResponseCode(404);
        $this->assertResponseContains('"error": "Aluno não encontrado."');
    }

    public function testAddMissingFields()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $student = $studentsTable->newEntity([
            'cpf' => '123.456.789-00',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birth_date' => '2000-01-01',
            'postal_code' => '12345-678',
            'street' => 'Main St',
            'house_number' => '123',
            'neighborhood' => 'Downtown',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '123456789',
            'whatsapp' => '987654321',
            'email' => 'johndoe@example.com',
        ]);
        $studentsTable->save($student);

        $payload = [
            'sub' => $student->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'input' => json_encode([
                'cpf' => '',
                'first_name' => '',
                'last_name' => '',
            ])
        ]);
        $this->post('/api/students/add');

        $this->assertResponseCode(400);
        $this->assertResponseContains('"error": "Erro ao cadastrar Aluno:"');
    }

    public function testEditInvalidCPF()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $student = $studentsTable->newEntity([
            'cpf' => '123.456.789-00',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birth_date' => '2000-01-01',
            'postal_code' => '12345-678',
            'street' => 'Main St',
            'house_number' => '123',
            'neighborhood' => 'Downtown',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '123456789',
            'whatsapp' => '987654321',
            'email' => 'johndoe@example.com',
        ]);
        $studentsTable->save($student);

        $payload = [
            'sub' => $student->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token],
            'input' => json_encode([
                'cpf' => 'invalid-cpf',
            ])
        ]);
        $this->put('/api/students/edit/' . $student->id);

        $this->assertResponseCode(400);
        $this->assertResponseContains('"error": "Erro ao atualizar Aluno:"');
    }

    public function testDeleteNonExistentStudent()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $student = $studentsTable->newEntity([
            'cpf' => '123.456.789-00',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'birth_date' => '2000-01-01',
            'postal_code' => '12345-678',
            'street' => 'Main St',
            'house_number' => '123',
            'neighborhood' => 'Downtown',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '123456789',
            'whatsapp' => '987654321',
            'email' => 'johndoe@example.com',
        ]);
        $studentsTable->save($student);

        $payload = [
            'sub' => $student->id,
            'iat' => time(),
            'exp' => time() + 3600,
        ];
        $secretKey = Configure::read('App.secretKey');
        $token = JWT::encode($payload, $secretKey);

        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer ' . $token]
        ]);
        $this->delete('/api/students/delete/9999');

        $this->assertResponseCode(404);
        $this->assertResponseContains('"error": "Aluno não encontrado."');
    }

    public function testIndexPagination()
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        for ($i = 1; $i <= 15; $i++) {
            $student = $studentsTable->newEntity([
                'cpf' => '123.456.789-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'first_name' => 'Student' . $i,
                'last_name' => 'Test',
                'birth_date' => '2000-01-01',
                'postal_code' => '12345-678',
                'street' => 'Main St',
                'house_number' => '123',
                'neighborhood' => 'Downtown',
                'state' => 'SP',
                'city' => 'São Paulo',
                'phone' => '123456789',
                'whatsapp' => '987654321',
                'email' => 'student' . $i . '@example.com',
            ]);
            $studentsTable->save($student);
        }

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
        $this->get('/api/students/index?page=2');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"students":');
        $this->assertResponseContains('"first_name": "Student11"');
    }
}
