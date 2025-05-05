<?php

declare(strict_types=1);

namespace App\Controller;

class GuardianFormsController extends AppController
{

    public function index()
    {
        $this->paginate = [
            'contain' => ['Lessons'],
        ];
        $guardianForms = $this->paginate($this->GuardianForms);

        $this->set(compact('guardianForms'));
    }

    public function view($id = null)
    {
        $guardianForm = $this->GuardianForms->get($id, [
            'contain' => ['Lessons'],
        ]);

        $this->set(compact('guardianForm'));
    }

    public function add()
    {
        $guardianForm = $this->GuardianForms->newEmptyEntity();
        if ($this->request->is('post')) {
            $guardianForm = $this->GuardianForms->patchEntity($guardianForm, $this->request->getData());
            if ($this->GuardianForms->save($guardianForm)) {
                $this->Flash->success(__('The guardian form has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The guardian form could not be saved. Please, try again.'));
        }
        $lessons = $this->GuardianForms->Lessons->find('list', ['limit' => 200])->all();
        $this->set(compact('guardianForm', 'lessons'));
    }

    public function edit($id = null)
    {
        $guardianForm = $this->GuardianForms->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $guardianForm = $this->GuardianForms->patchEntity($guardianForm, $this->request->getData());
            if ($this->GuardianForms->save($guardianForm)) {
                $this->Flash->success(__('The guardian form has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The guardian form could not be saved. Please, try again.'));
        }
        $lessons = $this->GuardianForms->Lessons->find('list', ['limit' => 200])->all();
        $this->set(compact('guardianForm', 'lessons'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $guardianForm = $this->GuardianForms->get($id);
        if ($this->GuardianForms->delete($guardianForm)) {
            $this->Flash->success(__('The guardian form has been deleted.'));
        } else {
            $this->Flash->error(__('The guardian form could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
