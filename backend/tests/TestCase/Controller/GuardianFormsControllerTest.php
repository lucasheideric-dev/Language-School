<?php

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class GuardianFormsControllerTest extends TestCase
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

        $this->get('/guardian-forms/index');

        $this->assertResponseOk();
        $this->assertResponseContains('GuardianForms');
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

        $this->get('/guardian-forms/view/1');

        $this->assertResponseOk();
        $this->assertResponseContains('GuardianForm');
        $this->assertResponseContains('signed');

        $guardianForms = $this->getTableLocator()->get('GuardianForms');
        $guardianForm = $guardianForms->get(1, ['contain' => ['Lessons']]);

        $this->assertResponseContains($guardianForm->pdf_path);
        $this->assertResponseContains($guardianForm->signed ? 'true' : 'false');
    }

    public function testAddGuardianForm()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $data = [
            'lesson_id' => 1,
            'signed' => true,
            'pdf_path' => '/path/to/pdf',
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/guardian-forms/add', $data);

        $this->assertResponseSuccess();

        $guardianForms = $this->getTableLocator()->get('GuardianForms');
        $query = $guardianForms->find()->where(['pdf_path' => '/path/to/pdf']);
        $this->assertEquals(1, $query->count());
    }

    public function testEditGuardianForm()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $guardianFormsTable = $this->getTableLocator()->get('GuardianForms');
        $guardianForm = $guardianFormsTable->newEntity([
            'lesson_id' => 1,
            'signed' => false,
            'pdf_path' => '/path/to/pdf',
        ]);
        $guardianFormsTable->save($guardianForm);

        $data = [
            'signed' => true,
            'pdf_path' => '/new/path/to/pdf',
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/guardian-forms/edit/' . $guardianForm->id, $data);

        $this->assertResponseSuccess();

        $updatedGuardianForm = $guardianFormsTable->get($guardianForm->id);
        $this->assertEquals('/new/path/to/pdf', $updatedGuardianForm->pdf_path);
        $this->assertTrue($updatedGuardianForm->signed);
    }

    public function testDeleteGuardianForm()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $guardianFormsTable = $this->getTableLocator()->get('GuardianForms');
        $guardianForm = $guardianFormsTable->newEntity([
            'lesson_id' => 1,
            'signed' => false,
            'pdf_path' => '/path/to/pdf',
        ]);
        $guardianFormsTable->save($guardianForm);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->delete('/guardian-forms/delete/' . $guardianForm->id);

        $this->assertResponseSuccess();
        $this->assertRedirect(['action' => 'index']);

        $query = $guardianFormsTable->find()->where(['id' => $guardianForm->id]);
        $this->assertEquals(0, $query->count());
    }
}
