<?php

declare(strict_types=1);

namespace App\Controller;

class TeachersController extends AppController
{
    public function index()
    {
        $this->paginate = [];
        $teachers = $this->paginate($this->Teachers);

        $this->set(compact('teachers'));
    }

    public function view($id = null)
    {
        $teacher = $this->Teachers->get($id, [
            'contain' => ['Lessons'],
        ]);

        $this->set(compact('teacher'));
    }

    public function add()
    {
        $teacher = $this->Teachers->newEmptyEntity();
        if ($this->request->is('post')) {
            $teacher = $this->Teachers->patchEntity($teacher, $this->request->getData());
            if ($this->Teachers->save($teacher)) {
                $this->Flash->success(__('Professor cadastrado com sucesso.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Não foi possível cadastrar o professor. Por favor, tente novamente.'));
        }
        $this->set(compact('teacher'));
    }

    public function edit($id = null)
    {
        if ($id === null) {
            $this->Flash->error(__('ID inválido. Por favor, tente novamente.'));
            return $this->redirect(['action' => 'index']);
        }

        $teacher = $this->Teachers->get($id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $teacher = $this->Teachers->patchEntity($teacher, $this->request->getData());
            if ($this->Teachers->save($teacher)) {
                $this->Flash->success(__('Professor atualizado com sucesso.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('Não foi possível atualizar o professor. Por favor, tente novamente.'));
        }
        $this->set(compact('teacher'));
    }

    public function delete($id = null)
    {
        if ($id === null) {
            $this->Flash->error(__('ID inválido. Não foi possível encontrar o professor.'));
            return $this->redirect(['action' => 'index']);
        }

        $teacher = $this->Teachers->get($id);
        if ($this->Teachers->delete($teacher)) {
            $this->Flash->success(__('Professor deletado com sucesso.'));
        } else {
            $this->Flash->error(__('Não foi possível deletar o professor. Tente novamente.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
