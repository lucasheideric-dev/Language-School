<?php

declare(strict_types=1);

namespace App\Controller;

class LessonsController extends AppController
{
    public function index()
    {
        $this->paginate = [
            'contain' => ['Students', 'Teachers'],
        ];
        $lessons = $this->paginate($this->Lessons);

        $this->set(compact('lessons'));
    }


    public function view($id = null)
    {
        $lesson = $this->Lessons->get($id, [
            'contain' => ['Students', 'Teachers'],
        ]);

        $this->set(compact('lesson'));
    }

    public function add()
    {
        $lesson = $this->Lessons->newEmptyEntity();
        if ($this->request->is('post')) {
            $lesson = $this->Lessons->patchEntity($lesson, $this->request->getData());
            if ($this->Lessons->save($lesson)) {
                $this->Flash->success(__('The lesson has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The lesson could not be saved. Please, try again.'));
        }
        $students = $this->Lessons->Students->find('list', ['limit' => 200])->all();
        $teachers = $this->Lessons->Teachers->find('list', ['limit' => 200])->all();
        $this->set(compact('lesson', 'students', 'teachers'));
    }

    public function edit($id = null)
    {
        $lesson = $this->Lessons->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $lesson = $this->Lessons->patchEntity($lesson, $this->request->getData());
            if ($this->Lessons->save($lesson)) {
                $this->Flash->success(__('The lesson has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The lesson could not be saved. Please, try again.'));
        }
        $students = $this->Lessons->Students->find('list', ['limit' => 200])->all();
        $teachers = $this->Lessons->Teachers->find('list', ['limit' => 200])->all();
        $this->set(compact('lesson', 'students', 'teachers'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $lesson = $this->Lessons->get($id);
        if ($this->Lessons->delete($lesson)) {
            $this->Flash->success(__('The lesson has been deleted.'));
        } else {
            $this->Flash->error(__('The lesson could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
