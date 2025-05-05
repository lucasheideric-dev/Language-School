<?php

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class StudentsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    public function testIndex()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $this->get('/students/index');

        $this->assertResponseOk();
        $this->assertResponseContains('Students');
        $this->assertResponseContains('id');
    }

    public function testView()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $this->get('/students/view/1');

        $this->assertResponseOk();
        $this->assertResponseContains('Student');
        $this->assertResponseContains('cpf');

        $students = $this->getTableLocator()->get('Students');
        $student = $students->get(1, ['contain' => ['Lessons']]);

        $this->assertResponseContains($student->first_name);
        $this->assertResponseContains($student->last_name);
        $this->assertResponseContains($student->cpf);
        foreach ($student->lessons as $lesson) {
            $this->assertResponseContains($lesson->title);
        }
    }

    public function testAddStudent()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $data = [
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'postal_code' => '12345-678',
            'street' => 'Rua Exemplo',
            'house_number' => '123',
            'neighborhood' => 'Bairro Exemplo',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '11999999999',
            'whatsapp' => '11999999999',
            'email' => 'joao.silva@example.com',
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/students/add', $data);

        $this->assertResponseSuccess();

        $students = $this->getTableLocator()->get('Students');
        $query = $students->find()->where(['cpf' => '123.456.789-10']);
        $this->assertEquals(1, $query->count());
    }

    public function testGetEditStudent()
    {
        $this->enableRetainFlashMessages();

        $this->session([
            'Auth' => [
                'User' => [
                    'id' => 1,
                    'email' => 'admin@gmail.com',
                ]
            ]
        ]);

        $studentsTable = $this->getTableLocator()->get('Students');
        $student = $studentsTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'postal_code' => '12345-678',
            'street' => 'Rua Exemplo',
            'house_number' => '123',
            'neighborhood' => 'Bairro Exemplo',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '11999999999',
            'whatsapp' => '11999999999',
            'email' => 'joao.silva@example.com',
        ]);
        $studentsTable->save($student);

        $this->get('/students/edit/' . $student->id);

        $this->assertResponseOk();

        $this->assertResponseContains('João');
        $this->assertResponseContains('Silva');
        $this->assertResponseContains('123.456.789-10');
    }

    public function testDeleteStudent()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $studentsTable = $this->getTableLocator()->get('Students');
        $student = $studentsTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'postal_code' => '12345-678',
            'street' => 'Rua Exemplo',
            'house_number' => '123',
            'neighborhood' => 'Bairro Exemplo',
            'state' => 'SP',
            'city' => 'São Paulo',
            'phone' => '11999999999',
            'whatsapp' => '11999999999',
            'email' => 'joao.silva@example.com',
        ]);
        $studentsTable->save($student);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->delete('/students/delete/' . $student->id);

        $this->assertResponseSuccess();
        $this->assertRedirect(['action' => 'index']);

        $query = $studentsTable->find()->where(['id' => $student->id]);
        $this->assertEquals(0, $query->count());
    }
}
