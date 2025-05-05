<?php

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class TeachersControllerTest extends TestCase
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

        $this->get('/teachers/index');

        $this->assertResponseOk();
        $this->assertResponseContains('Teachers');
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

        $this->get('/teachers/view/1');

        $this->assertResponseOk();
        $this->assertResponseContains('Teacher');
        $this->assertResponseContains('cpf');

        $teachers = $this->getTableLocator()->get('Teachers');
        $teacher = $teachers->get(1, ['contain' => ['Lessons']]);

        $this->assertResponseContains($teacher->name);
        $this->assertResponseContains($teacher->user->name);
        $this->assertResponseContains($teacher->cpf);
        foreach ($teacher->lessons as $lesson) {
            $this->assertResponseContains($lesson->title);
        }
    }

    public function testAddTeacher()
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
            'specialty' => 'Matemática',
            'status' => true,
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/teachers/add', $data);

        $this->assertResponseSuccess();

        $teachers = $this->getTableLocator()->get('Teachers');
        $query = $teachers->find()->where(['cpf' => '123.456.789-10']);
        $this->assertEquals(1, $query->count());
    }

    public function testGetEditTeacher()
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

        $teachersTable = $this->getTableLocator()->get('Teachers');
        $teacher = $teachersTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'specialty' => 'Matemática',
            'status' => true,
        ]);
        $teachersTable->save($teacher);

        $this->get('/teachers/edit/' . $teacher->id);

        $this->assertResponseOk();

        $this->assertResponseContains('João');
        $this->assertResponseContains('Silva');
        $this->assertResponseContains('123.456.789-10');
    }

    public function testDeleteTeacher()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $teachersTable = $this->getTableLocator()->get('Teachers');
        $teacher = $teachersTable->newEntity([
            'cpf' => '123.456.789-10',
            'first_name' => 'João',
            'last_name' => 'Silva',
            'birth_date' => '1985-05-10',
            'specialty' => 'Matemática',
            'status' => true,
        ]);
        $teachersTable->save($teacher);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->delete('/teachers/delete/' . $teacher->id);

        $this->assertResponseSuccess();
        $this->assertRedirect(['action' => 'index']);

        $query = $teachersTable->find()->where(['id' => $teacher->id]);
        $this->assertEquals(0, $query->count());
    }
}
