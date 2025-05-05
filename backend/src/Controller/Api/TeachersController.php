<?php

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TeachersController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Teachers');
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
            $teachers = $this->Teachers->find('all')->toArray();
            return $this->jsonResponse(['teachers' => $teachers], 200);
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
            $teachers = $this->Teachers->find('list');
            return $this->jsonResponse(['teachers' => $teachers], 200);
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

            $teacher = $this->Teachers->find()
                ->where(['Teachers.id' => $id])
                ->first();

            if ($teacher) {
                return $this->jsonResponse(['teacher' => $teacher], 200);
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

            $teacher = $this->Teachers->newEntity($this->request->getData());

            if ($this->Teachers->save($teacher)) {
                return $this->jsonResponse(['success' => 'Professor cadastrado com sucesso.'], 201);
            } else {
                $errors = $teacher->getErrors();
                $firstError = array_values($errors)[0];
                $firstMessage = is_array($firstError) ? array_values($firstError)[0] : 'Erro desconhecido.';

                return $this->jsonResponse([
                    'error' => 'Erro ao cadastrar Professor: ' . $firstMessage
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

            $teacher = $this->Teachers->get($id);

            $teacher = $this->Teachers->patchEntity($teacher, $this->request->getData());
            $teacher->updated_at = date('Y-m-d H:i:s');

            if ($this->Teachers->save($teacher)) {
                return $this->jsonResponse(['success' => 'Professor atualizado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao atualizar professor.', 'details' => $teacher->getErrors()], 400);
            }
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

            $teacher = $this->Teachers->get($id);

            if ($this->Teachers->delete($teacher)) {
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

    private function jsonResponse(array $data, int $status = 200)
    {
        $this->response = $this->response->withType('application/json')
            ->withStatus($status);
        $this->response->getBody()->write(json_encode($data));
        return $this->response;
    }
}
