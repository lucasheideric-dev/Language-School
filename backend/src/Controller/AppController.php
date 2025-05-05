<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Controller\Controller;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');

        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password'
                    ],
                    'userModel' => 'Users',
                ],
            ],
            'authorize' => 'Controller',
            'loginRedirect' => ['controller' => 'Dashboard', 'action' => 'index'],
            'logoutRedirect' => ['controller' => 'Users', 'action' => 'login'],
        ]);
    }

    public function isAuthorized($user)
    {
        return true;
    }

    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        if ($this->components()->has('Auth')) {
            $this->set('current_user', $this->Auth->user());
        }
    }
}
