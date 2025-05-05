<?php

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class GuardianFormsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadModel('GuardianForms');
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
            $forms = $this->GuardianForms->find('all')->toArray();
            return $this->jsonResponse(['forms' => $forms]);
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

            $form = $this->GuardianForms->find()
                ->where(['GuardianForms.id' => $id])
                ->first();

            if ($form) {
                return $this->jsonResponse(['form' => $form]);
            } else {
                return $this->jsonResponse(['error' => 'Usuário não encontrado.'], 404);
            }
        } catch (\Exception $e) {
            return $this->jsonResponse(['error' => 'Token inválido ou expirado.'], 401);
        }
    }

    public function add()
    {
        $this->loadModel('GuardianForms');
        $this->request->allowMethod(['post']);
        $authorizationHeader = $this->request->getHeaderLine('Authorization');

        if (!$authorizationHeader || !str_starts_with($authorizationHeader, 'Bearer ')) {
            return $this->jsonResponse(['error' => 'Token não fornecido.'], 401);
        }

        $token = str_replace('Bearer ', '', $authorizationHeader);
        $secretKey = Configure::read('App.secretKey');

        try {
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            $form = $this->GuardianForms->newEntity($this->request->getData());

            if ($this->GuardianForms->save($form)) {
                return $this->jsonResponse(['success' => 'Usuário cadastrado com sucesso.'], 201);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao cadastrar usuário.', 'details' => $form->getErrors()], 400);
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

            $form = $this->GuardianForms->get($id);

            $form = $this->GuardianForms->patchEntity($form, $this->request->getData());

            if ($this->GuardianForms->save($form)) {
                return $this->jsonResponse(['success' => 'Usuário atualizado com sucesso.'], 200);
            } else {
                return $this->jsonResponse(['error' => 'Erro ao atualizar usuário.', 'details' => $form->getErrors()], 400);
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

            $form = $this->GuardianForms->get($id);

            if ($this->GuardianForms->delete($form)) {
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
