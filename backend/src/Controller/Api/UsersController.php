<?php

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UsersController extends AppController
{
    public $Users = null;

    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('Users');
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
            $users = $this->Users->find('all')->toArray();
            return $this->jsonResponse(['users' => $users]);
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
            $users = $this->Users->find('list');
            return $this->jsonResponse(['users' => $users]);
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

            $user = $this->Users->find()
                ->where(['Users.id' => $id])
                ->first();

            if ($user) {
                return $this->jsonResponse(['user' => $user]);
            } else {
                return $this->jsonResponse(['error' => 'Usuário não encontrado.'], 404);
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

            $user = $this->Users->newEntity($this->request->getData());

            if ($this->Users->save($user)) {
                return $this->jsonResponse(['success' => 'Usuário cadastrado com sucesso.'], 201);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao cadastrar usuário.', 'details' => $user->getErrors()], 400);
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

            $user = $this->Users->get($id);

            $user = $this->Users->patchEntity($user, $this->request->getData());

            if ($this->Users->save($user)) {
                return $this->jsonResponse(['success' => 'Usuário atualizado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao atualizar usuário.', 'details' => $user->getErrors()], 400);
            }
        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            return $this->jsonResponse(['error' => 'Usuário não encontrado.'], 404);
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

            $user = $this->Users->get($id);

            if ($this->Users->delete($user)) {
                return $this->jsonResponse(['success' => 'Usuário deletado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao deletar usuário.'], 400);
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
