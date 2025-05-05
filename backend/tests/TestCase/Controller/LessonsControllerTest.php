<?php

namespace App\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

class LessonsControllerTest extends TestCase
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

        $this->get('/lessons/index');

        $this->assertResponseOk();
        $this->assertResponseContains('Lessons');
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

        $this->get('/lessons/view/1');

        $this->assertResponseOk();
        $this->assertResponseContains('Lesson');
        $this->assertResponseContains('content');

        $lessons = $this->getTableLocator()->get('Lessons');
        $lesson = $lessons->get(1, ['contain' => ['Students', 'Teachers']]);

        $this->assertResponseContains($lesson->content);
        $this->assertResponseContains($lesson->status);
    }

    public function testAddLesson()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $data = [
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-10-01 10:00:00',
            'content' => 'Mathematics',
            'status' => 'Scheduled',
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->post('/lessons/add', $data);

        $this->assertResponseSuccess();

        $lessons = $this->getTableLocator()->get('Lessons');
        $query = $lessons->find()->where(['content' => 'Mathematics']);
        $this->assertEquals(1, $query->count());
    }

    public function testEditLesson()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $lessonsTable = $this->getTableLocator()->get('Lessons');
        $lesson = $lessonsTable->newEntity([
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-10-01 10:00:00',
            'content' => 'Mathematics',
            'status' => 'Scheduled',
        ]);
        $lessonsTable->save($lesson);

        $data = [
            'content' => 'Physics',
            'status' => 'Completed',
        ];

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->put('/lessons/edit/' . $lesson->id, $data);

        $this->assertResponseSuccess();

        $updatedLesson = $lessonsTable->get($lesson->id);
        $this->assertEquals('Physics', $updatedLesson->content);
        $this->assertEquals('Completed', $updatedLesson->status);
    }

    public function testDeleteLesson()
    {
        $this->session([
            'Auth.User' => [
                'id' => 1,
                'email' => 'admin@gmail.com',
                'password' => '$2y$10$vvOqjJcO8QKnueJ1s7oxXOa7hsm9dKQMtBu7JsmID8VYIoxO3zjv2',
            ]
        ]);

        $lessonsTable = $this->getTableLocator()->get('Lessons');
        $lesson = $lessonsTable->newEntity([
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-10-01 10:00:00',
            'content' => 'Mathematics',
            'status' => 'Scheduled',
        ]);
        $lessonsTable->save($lesson);

        $this->enableCsrfToken();
        $this->enableSecurityToken();
        $this->delete('/lessons/delete/' . $lesson->id);

        $this->assertResponseSuccess();
        $this->assertRedirect(['action' => 'index']);

        $query = $lessonsTable->find()->where(['id' => $lesson->id]);
        $this->assertEquals(0, $query->count());
    }
}
