<?php

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class UsersControllerTest extends TestCase
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

        $this->get('/users/index');

        $this->assertResponseOk();
        $this->assertResponseContains('Users');
        $this->assertResponseContains('id');
    }

    public function testLogout()
    {
        $this->session(['Auth.User' => [
            'id' => 1,
            'name' => 'Lucas',
            'last_name' => 'Heideric',
            'email' => 'lucas@gmail.com',
            'password' => '$2y$10$phh7xbfTwsGsnsetR.4pjuamrBQNFc1Vy3X38qqTNGqVC8SoFKNau',
            'role' => '1',
        ]]);

        $this->get('/users/logout');
        $this->assertSession(null, 'Auth.User');
        $this->assertRedirect(['action' => 'login']);
    }

    public function testLogin()
    {
        $this->get('/users/login');
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

        $this->get('/users/view/1');

        $this->assertResponseOk();
        $this->assertResponseContains('User');
        $this->assertResponseContains('id');
        $this->assertResponseContains('email');
    }

    public function testAdd()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $data = [
            'first_name' => 'New User',
            'last_name' => 'Test',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'created_at' => '2023-10-01 12:00:00',
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/users/add', $data);

        $this->assertResponseSuccess();
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find()->where(['email' => 'newuser@example.com']);
        $this->assertEquals(1, $query->count());
    }

    public function testEdit()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $data = [
            'first_name' => 'Updated User',
            'last_name' => 'Test',
            'email' => 'updateduser@example.com',
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/users/edit/1', $data);

        $this->assertResponseSuccess();
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find()->where(['email' => 'updateduser@example.com']);
        $this->assertEquals(1, $query->count());
    }

    public function testDelete()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/users/delete/1');

        $this->assertResponseSuccess();
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find()->where(['id' => 1]);
        $this->assertEquals(0, $query->count());
    }
}
