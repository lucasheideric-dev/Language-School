<?php

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class LessonsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Lessons');
        $this->loadComponent('RequestHandler');
    }

    public function index()
    {
        $this->request->allowMethod(['get']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $lessons = $this->Lessons->find('all', ['contain' => ['Students', 'Teachers']])->toArray();
            return $this->jsonResponse(['lessons' => $lessons]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function today()
    {
        $this->request->allowMethod(['get']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $lessons = $this->Lessons->find('all', [
                'conditions' => [
                    'lesson_date >=' => date('Y-m-d 00:00:00'),
                    'lesson_date <=' => date('Y-m-d 23:59:59')
                ],
                'contain' => ['Students', 'Teachers']
            ])->toArray();
            return $this->jsonResponse(['lessons' => $lessons]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function lastLessons()
    {
        $this->request->allowMethod(['get']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));
            $lessons = $this->Lessons->find('all', [
                'order' => ['lesson_date' => 'DESC'],
                'contain' => ['Students', 'Teachers'],
                'limit' => 5
            ])->toArray();
            return $this->jsonResponse(['lessons' => $lessons]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function view($id = null)
    {
        $this->request->allowMethod(['get']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $lesson = $this->Lessons->find()
                ->where(['Lessons.id' => $id])
                ->first();

            if ($lesson) {
                return $this->jsonResponse(['lesson' => $lesson]);
            } else {
                return $this->jsonResponse(['error' => 'Professor não encontrado.'], 404);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function add()
    {
        $this->request->allowMethod(['post']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $this->loadModel('Teachers');
            $this->loadModel('Lessons');
            $this->loadModel('Students');

            $data = $this->request->getData();

            if (!empty($data['lesson_date'])) {
                $data['lesson_date'] = str_replace('T', ' ', $data['lesson_date']);
                if (strlen($data['lesson_date']) == 16) {
                    $data['lesson_date'] .= ':00';
                }
            }

            // Não pode agendar com menos de 24 horas de antecedência
            if (!empty($data['lesson_date'])) {
                $lessonDateTime = new \DateTime($data['lesson_date']);
                $now = new \DateTime();

                $diffInHours = ($lessonDateTime->getTimestamp() - $now->getTimestamp()) / 3600;

                if ($diffInHours < 24) {
                    return $this->jsonResponse([
                        'error' => 'O agendamento deve ser realizado com pelo menos 24 horas de antecedência.'
                    ], 400);
                }
            }

            // Verificar se já existe uma aula no mesmo horário
            $existingLesson = $this->Lessons->find()
                ->where([
                    'teacher_id' => $data['teacher_id'],
                    'lesson_date' => $data['lesson_date']
                ])
                ->first();

            if ($existingLesson) {
                return $this->jsonResponse([
                    'error' => 'Já existe uma aula para este professor no mesmo horário.'
                ], 400);
            }

            // Verificar se já existem 2 aulas no mesmo dia
            if (!empty($data['teacher_id']) && !empty($data['lesson_date'])) {
                $dateOnly = date('Y-m-d', strtotime($data['lesson_date']));

                $lessonsSameDay = $this->Lessons->find()
                    ->where([
                        'teacher_id' => $data['teacher_id'],
                        "DATE(lesson_date) =" => $dateOnly
                    ])
                    ->count();

                if ($lessonsSameDay >= 2) {
                    return $this->jsonResponse([
                        'error' => 'O professor já possui duas aulas agendadas para este dia.'
                    ], 400);
                }
            }

            // Verificar idade do aluno
            if (!empty($data['student_id'])) {
                $student = $this->Students->find()
                    ->where(['id' => $data['student_id']])
                    ->first();

                if ($student && !empty($student->birth_date)) {
                    $birthDate = new \DateTime($student->birth_date);
                    $today = new \DateTime();
                    $age = $today->diff($birthDate)->y;

                    if ($age < 16) {
                        // Permite agendar, mas sinaliza que o aluno é menor de idade
                        $lesson = $this->Lessons->newEntity($data);

                        // Salva a aula mesmo com o aluno menor
                        if (!$this->Lessons->save($lesson)) {
                            $errors = $lesson->getErrors();
                            $firstError = array_values($errors)[0];
                            $firstMessage = is_array($firstError) ? array_values($firstError)[0] : 'Erro desconhecido.';

                            return $this->jsonResponse([
                                'error' => 'Erro ao cadastrar Aula: ' . $firstMessage
                            ], 400);
                        }

                        return $this->jsonResponse([
                            'success' => 'Aula agendada com sucesso.',
                            'lesson_id' => $lesson->id,  // Retorna o ID da aula agendada
                            'minor' => true,  // Indica que o aluno é menor de idade
                            'message' => 'O aluno é menor de idade (menos de 16 anos).',
                            'student_id' => $student->id,
                            'teacher_id' => $data['teacher_id'],
                        ], 201);
                    }
                }
            }

            // Salvar a aula normalmente, caso o aluno seja maior de idade
            $lesson = $this->Lessons->newEntity($data);

            if (!$this->Lessons->save($lesson)) {
                $errors = $lesson->getErrors();
                $firstError = array_values($errors)[0];
                $firstMessage = is_array($firstError) ? array_values($firstError)[0] : 'Erro desconhecido.';

                return $this->jsonResponse([
                    'error' => 'Erro ao cadastrar Aula: ' . $firstMessage
                ], 400);
            }

            return $this->jsonResponse(['success' => 'Aula agendada com sucesso.'], 201);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function edit($id = null)
    {
        $this->request->allowMethod(['put', 'patch', 'post']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $this->loadModel('Lessons');

            $lesson = $this->Lessons->get($id);
            $data = $this->request->getData();

            if (!empty($data['lesson_date'])) {
                $data['lesson_date'] = str_replace('T', ' ', $data['lesson_date']);
                if (strlen($data['lesson_date']) == 16) {
                    $data['lesson_date'] .= ':00';
                }
            }

            $originalTeacherId = $lesson->teacher_id;
            $originalLessonDate = $lesson->lesson_date;

            $teacherChanged = isset($data['teacher_id']) && $data['teacher_id'] != $originalTeacherId;
            $dateChanged = isset($data['lesson_date']) && $data['lesson_date'] != $originalLessonDate;

            if ($teacherChanged || $dateChanged) {
                $currentDateOnly = date('Y-m-d', strtotime($originalLessonDate));
                $newDateOnly = date('Y-m-d', strtotime($data['lesson_date']));

                $dayChanged = $currentDateOnly !== $newDateOnly;

                if ($dayChanged) {
                    // 1. Verificar 24h de antecedência
                    $lessonDateTime = new \DateTime($data['lesson_date']);
                    $now = new \DateTime();
                    $diffInHours = ($lessonDateTime->getTimestamp() - $now->getTimestamp()) / 3600;

                    if ($diffInHours < 24) {
                        return $this->jsonResponse([
                            'error' => 'O agendamento deve ser realizado com pelo menos 24 horas de antecedência.'
                        ], 400);
                    }

                    // 2. Verificar se já existem 2 aulas no mesmo novo dia
                    $lessonsSameDay = $this->Lessons->find()
                        ->where([
                            'teacher_id' => $data['teacher_id'],
                            "DATE(lesson_date) =" => $newDateOnly,
                            'id !=' => $id
                        ])
                        ->count();

                    if ($lessonsSameDay >= 2) {
                        return $this->jsonResponse([
                            'error' => 'O professor já possui duas aulas agendadas para este dia.'
                        ], 400);
                    }
                }

                // 3. Em todos os casos (mesmo mudando só horário), checar conflito de horário
                $existingLesson = $this->Lessons->find()
                    ->where([
                        'teacher_id' => $data['teacher_id'],
                        'lesson_date' => $data['lesson_date'],
                        'id !=' => $id
                    ])
                    ->first();

                if ($existingLesson) {
                    return $this->jsonResponse([
                        'error' => 'Já existe uma aula para este professor no mesmo horário.'
                    ], 400);
                }
            }

            // Patch entity e salvar
            $lesson = $this->Lessons->patchEntity($lesson, $data);
            $lesson->updated_at = date('Y-m-d H:i:s');

            if (!$this->Lessons->save($lesson)) {
                $errors = $lesson->getErrors();
                $firstError = array_values($errors)[0];
                $firstMessage = is_array($firstError) ? array_values($firstError)[0] : 'Erro desconhecido.';

                return $this->jsonResponse([
                    'error' => 'Erro ao atualizar Aula: ' . $firstMessage
                ], 400);
            }

            return $this->jsonResponse(['success' => 'Professor atualizado com sucesso.'], 200);
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Professor não encontrado.'], 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['delete', 'post']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $lesson = $this->Lessons->get($id);

            if ($this->Lessons->delete($lesson)) {
                return $this->jsonResponse(['success' => 'Professor deletado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao deletar professor.'], 400);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Professor não encontrado.'], 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function playLesson($id = null)
    {
        $this->request->allowMethod(['put', 'get']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $lesson = $this->Lessons->get($id);
            $lesson->status = 'Em andamento';

            if ($this->Lessons->save($lesson)) {
                return $this->jsonResponse(['success' => 'Aula atualizada com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao atualizar aula.', 'details' => $lesson->getErrors()], 400);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Aula não encontrado.'], 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function finishLesson($id = null)
    {
        $this->request->allowMethod(['put', 'get']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $lesson = $this->Lessons->get($id);
            $lesson->status = 'Finalizada';

            if ($this->Lessons->save($lesson)) {
                return $this->jsonResponse(['success' => 'Usuário atualizado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao atualizar usuário.', 'details' => $lesson->getErrors()], 400);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Usuário não encontrado.'], 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function cancelLesson($id = null)
    {
        $this->request->allowMethod(['put', 'get']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $lesson = $this->Lessons->get($id);
            $lesson->status = 'Cancelada';

            if ($this->Lessons->save($lesson)) {
                return $this->jsonResponse(['success' => 'Usuário atualizado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao atualizar usuário.', 'details' => $lesson->getErrors()], 400);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Usuário não encontrado.'], 404);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    private function jsonResponse(array $data, int $status = 200)
    {
        $this->response = $this->response->withType('application/json')
            ->withStatus($status);
        $this->response->getBody()->write(json_encode($data));
        return $this->response;
    }
}
