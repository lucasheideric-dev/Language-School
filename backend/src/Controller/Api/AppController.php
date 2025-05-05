<?php

namespace App\Controller\Api;

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\Http\Response;

class AppController extends Controller
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->Auth = null;
    }

    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Atualiza a instância da response com os headers CORS
        $this->response = $this->response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS')
            ->withHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Max-Age', '86400');

        // Se for uma requisição OPTIONS, retorna imediatamente com status 204 e os headers já setados
        if ($this->request->is('options')) {
            return $this->response->withStatus(204);
        }
    }
}
