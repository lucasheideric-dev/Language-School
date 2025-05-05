<?php

namespace App\Controller\Api;

use App\Controller\Api\AppController;
use Cake\Core\Configure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends AppController
{
    public function login()
    {
        $this->request->allowMethod(['post', 'get']);
        $this->loadModel('Users');

        $secretKey = Configure::read('App.secretKey');

        $requestData = $this->request->getData();
        $email = $requestData['email'] ?? null;
        $password = $requestData['password'] ?? null;

        $this->response = $this->response->withType('json');

        if (!$email || !$password) {
            return $this->jsonResponse(['error' => 'Dados incompletos.']);
        }

        $user = $this->Users->find('all', [
            'conditions' => ['Users.email' => $email]
        ])->first();

        if (!$user || !password_verify($password, $user->password)) {
            return $this->jsonResponse(['error' => 'E-mail ou senha incorretos.']);
        }

        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + 3600
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        unset($user->password);

        return $this->jsonResponse([
            'user' => $user,
            'token' => $jwt,
        ]);
    }

    private function jsonResponse(array $data)
    {
        $this->response->getBody()->write(json_encode($data));
        return $this->response;
    }
}
