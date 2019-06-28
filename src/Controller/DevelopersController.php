<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Developers Controller
 *
 * @property \App\Model\Table\DevelopersTable $Developers
 */
class DevelopersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'conditions' => ['state' => 1],
            'contain' => ['Customers' => function($q){
                return $q->find('all')->select('Customers.developer_id');
            }]
        ];
        $developers = $this->paginate($this->Developers);

        $this->set(compact('developers'));
        $this->set('_serialize', ['developers']);
    }

    /**
     * View method
     *
     * @param string|null $id Developer id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

        $developer = $this->Developers->get($id, [
            'contain' => ['Customers' => function($q){
                return $q->contain(['Users', 'Developers'])
                         ->order('Customers.modified Desc');
            }]
        ]);
        $stateArr = [0 => '進行中',1 => '已完成'];

        $this->set(compact('stateArr'));
        $this->set('developer', $developer);
        $this->set('_serialize', ['developer']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        $developer = $this->Developers->newEntity();
        if ($this->request->is('post')) {
            $developer = $this->Developers->patchEntity($developer, $this->request->getData());
            if ($this->Developers->save($developer)) {
                $this->Flash->success(__('The developer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The developer could not be saved. Please, try again.'));
        }
        $users = $this->Developers->Users->find('list', ['limit' => 200]);
        $this->set(compact('developer', 'users'));
        $this->set('_serialize', ['developer']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Developer id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {


        $developer = $this->Developers->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $developer = $this->Developers->patchEntity($developer, $this->request->getData());
            if ($this->Developers->save($developer)) {
                $this->Flash->success(__('The developer has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The developer could not be saved. Please, try again.'));
        }
        $this->set(compact('developer'));
        $this->set('_serialize', ['developer']);
    }
}
