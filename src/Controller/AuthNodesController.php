<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * AuthNodes Controller
 *
 * @property \App\Model\Table\AuthNodesTable $AuthNodes
 */
class AuthNodesController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['ParentAuthNodes']
        ];
        $authNodes = $this->paginate($this->AuthNodes);

        $this->set(compact('authNodes'));
        $this->set('_serialize', ['authNodes']);
    }

    /**
     * View method
     *
     * @param string|null $id Auth Node id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $authNode = $this->AuthNodes->get($id, [
            'contain' => ['ParentAuthNodes', 'ChildAuthNodes']
        ]);

        $this->set('authNode', $authNode);
        $this->set('_serialize', ['authNode']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $authNode = $this->AuthNodes->newEntity();
        if ($this->request->is('post')) {
            $authNode = $this->AuthNodes->patchEntity($authNode, $this->request->getData());
            if ($this->AuthNodes->save($authNode)) {
                $this->Flash->success(__('The auth node has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The auth node could not be saved. Please, try again.'));
        }
        $parentAuthNodes = $this->AuthNodes->ParentAuthNodes->find('list', ['limit' => 200]);
        $this->set(compact('authNode', 'parentAuthNodes'));
        $this->set('_serialize', ['authNode']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Auth Node id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $authNode = $this->AuthNodes->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $authNode = $this->AuthNodes->patchEntity($authNode, $this->request->getData());
            if ($this->AuthNodes->save($authNode)) {
                $this->Flash->success(__('The auth node has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The auth node could not be saved. Please, try again.'));
        }
        $parentAuthNodes = $this->AuthNodes->ParentAuthNodes->find('list', ['limit' => 200]);
        $this->set(compact('authNode', 'parentAuthNodes'));
        $this->set('_serialize', ['authNode']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Auth Node id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $authNode = $this->AuthNodes->get($id);
        if ($this->AuthNodes->delete($authNode)) {
            $this->Flash->success(__('The auth node has been deleted.'));
        } else {
            $this->Flash->error(__('The auth node could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
