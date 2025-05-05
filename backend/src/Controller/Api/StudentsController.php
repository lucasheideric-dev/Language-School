<?php

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class StudentsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Students');
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
            $students = $this->Students->find('all')->toArray();
            return $this->jsonResponse(['students' => $students]);
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function list()
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
            $students = $this->Students->find('list');
            return $this->jsonResponse(['students' => $students]);
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

            $student = $this->Students->find()
                ->where(['Students.id' => $id])
                ->first();

            if ($student) {
                return $this->jsonResponse(['student' => $student]);
            } else {
                return $this->jsonResponse(['error' => 'Aluno não encontrado.'], 404);
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
            $student = $this->Students->newEntity($this->request->getData());

            if ($this->Students->save($student)) {
                return $this->jsonResponse(['success' => 'Aluno cadastrado com sucesso.'], 201);
            } else {
                $errors = $student->getErrors();
                $firstError = array_values($errors)[0];
                $firstMessage = is_array($firstError) ? array_values($firstError)[0] : 'Erro desconhecido.';

                return $this->jsonResponse([
                    'error' => 'Erro ao cadastrar Aluno: ' . $firstMessage
                ], 400);
            }
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

            $student = $this->Students->get($id);

            $student = $this->Students->patchEntity($student, $this->request->getData());

            if ($this->Students->save($student)) {
                return $this->jsonResponse(['success' => 'Aluno atualizado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao atualizar Aluno.', 'details' => $student->getErrors()], 400);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Aluno não encontrado.'], 404);
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

            $student = $this->Students->get($id);

            if ($this->Students->delete($student)) {
                return $this->jsonResponse(['success' => 'Aluno deletado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao deletar Aluno.'], 400);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Aluno não encontrado.'], 404);
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
