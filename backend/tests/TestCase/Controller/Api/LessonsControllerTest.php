<?php

namespace App\Test\TestCase\Controller\Api;

use Cake\TestSuite\IntegrationTestCase;
use Firebase\JWT\JWT;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;

class LessonsControllerTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('App.secretKey', 'test_secret_key');
    }

    public function testIndexValidToken()
    {
        $lessonTable = TableRegistry::getTableLocator()->get('Lessons');
        $lesson = $lessonTable->newEntity([
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-12-01 10:00:00',
            'content' => 'Math Lesson',
        ]);
        $lessonTable->save($lesson);

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
        $this->get('/api/lessons/index');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"lessons":');
    }

    public function testIndexInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/lessons/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testIndexNoToken()
    {
        $this->get('/api/lessons/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testViewValidToken()
    {
        $lessonTable = TableRegistry::getTableLocator()->get('Lessons');
        $lesson = $lessonTable->newEntity([
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-12-01 10:00:00',
            'content' => 'Math Lesson',
        ]);
        $lessonTable->save($lesson);

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
        $this->get('/api/lessons/view/' . $lesson->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"lesson":');
        $this->assertResponseContains('"content": "Math Lesson"');
    }

    public function testViewInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/lessons/view/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testViewLessonNotFound()
    {
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
        $this->get('/api/lessons/view/9999');

        $this->assertResponseCode(404);
        $this->assertResponseContains('"error": "Professor não encontrado."');
    }

    public function testAddValidToken()
    {
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
                'student_id' => 1,
                'teacher_id' => 1,
                'lesson_date' => '2023-12-01 10:00:00',
                'content' => 'Math Lesson',
            ])
        ]);
        $this->post('/api/lessons/add');

        $this->assertResponseCode(201);
        $this->assertResponseContains('"success": "Aula agendada com sucesso."');
    }

    public function testAddInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token'],
            'input' => json_encode([
                'student_id' => 1,
                'teacher_id' => 1,
                'lesson_date' => '2023-12-01 10:00:00',
                'content' => 'Math Lesson',
            ])
        ]);
        $this->post('/api/lessons/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testAddNoToken()
    {
        $this->configRequest([
            'input' => json_encode([
                'student_id' => 1,
                'teacher_id' => 1,
                'lesson_date' => '2023-12-01 10:00:00',
                'content' => 'Math Lesson',
            ])
        ]);
        $this->post('/api/lessons/add');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testDeleteValidToken()
    {
        $lessonTable = TableRegistry::getTableLocator()->get('Lessons');
        $lesson = $lessonTable->newEntity([
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-12-01 10:00:00',
            'content' => 'Math Lesson',
        ]);
        $lessonTable->save($lesson);

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
        $this->delete('/api/lessons/delete/' . $lesson->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"success": "Professor deletado com sucesso."');
    }

    public function testDeleteInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->delete('/api/lessons/delete/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testDeleteNoToken()
    {
        $this->delete('/api/lessons/delete/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testEditValidToken()
    {
        $lessonTable = TableRegistry::getTableLocator()->get('Lessons');
        $lesson = $lessonTable->newEntity([
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-12-01 10:00:00',
            'content' => 'Math Lesson',
        ]);
        $lessonTable->save($lesson);

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
                'content' => 'Updated Lesson',
            ])
        ]);
        $this->put('/api/lessons/edit/' . $lesson->id);

        $this->assertResponseCode(200);
        $this->assertResponseContains('"success": "Professor atualizado com sucesso."');
    }

    public function testEditInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token'],
            'input' => json_encode([
                'content' => 'Updated Lesson',
            ])
        ]);
        $this->put('/api/lessons/edit/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testEditNoToken()
    {
        $this->configRequest([
            'input' => json_encode([
                'content' => 'Updated Lesson',
            ])
        ]);
        $this->put('/api/lessons/edit/1');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }

    public function testEditLessonNotFound()
    {
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
                'content' => 'Updated Lesson',
            ])
        ]);
        $this->put('/api/lessons/edit/9999');

        $this->assertResponseCode(404);
        $this->assertResponseContains('"error": "Professor não encontrado."');
    }

    public function testListValidToken()
    {
        $lessonTable = TableRegistry::getTableLocator()->get('Lessons');
        $lesson = $lessonTable->newEntity([
            'student_id' => 1,
            'teacher_id' => 1,
            'lesson_date' => '2023-12-01 10:00:00',
            'content' => 'Math Lesson',
        ]);
        $lessonTable->save($lesson);

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
        $this->get('/api/lessons/index');

        $this->assertResponseCode(200);
        $this->assertResponseContains('"lessons":');
    }

    public function testListInvalidToken()
    {
        $this->configRequest([
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->get('/api/lessons/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token inválido ou expirado."');
    }

    public function testListNoToken()
    {
        $this->get('/api/lessons/index');

        $this->assertResponseCode(401);
        $this->assertResponseContains('"error": "Token não fornecido."');
    }
}
