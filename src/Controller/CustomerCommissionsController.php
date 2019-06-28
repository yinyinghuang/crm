<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * CustomerCommissions Controller
 *
 * @property \App\Model\Table\CustomerCommissionsTable $CustomerCommissions
 */
class CustomerCommissionsController extends AppController
{
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add($id)
    {
         switch ($this->_user['role_id']) {
            case 1:
                $conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $conditions['Users.role_id <= '] = $this->_user['role_id'];
                $conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $conditions = null; 
            break;
        }
        $this->loadModel('Customers');
        $results = $this->Customers->find('all',[
            'contain' => ['Users'],
            'conditions' => $conditions,
            'fields' => 'Customers.id'
        ]);

        foreach ($results as $v) {
            $idArr[] = $v['id'];
        }

        if(!in_array($id, $idArr)) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        }

        $customerCommission = $this->CustomerCommissions->newEntity();
        if ($this->request->is('post')) {
            $customerCommission = $this->CustomerCommissions->patchEntity($customerCommission, $this->request->getData());
            if ($this->CustomerCommissions->save($customerCommission)) {
                $this->Flash->success(__('The customer commission has been saved.'));

                return $this->redirect(['controller' => 'Customers','action' => 'view', $this->request->getdata('customer_id')]);
            }
            $this->Flash->error(__('The customer commission could not be saved. Please, try again.'));
        }

        $this->loadModel('Departments');
        $this->loadModel('Users');
        $departments  = $this->Departments->find('all')->select(['id','name']);
        foreach ($departments as $d) {
            $tem[$d->id] = $d->name;
        }
        $departments = $tem;
        unset($tem);
        $users  = $this->Users->find('all')->select(['id','username']);
        foreach ($users as $u) {
            $tem[$u->id] = $u->username;
        }
        $users = $tem;

        $this->set(compact('customerCommission', 'departments', 'users', 'id'));
        $this->set('_serialize', ['customerCommission']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Customer Commission id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {     
        switch ($this->_user['role_id']) {
            case 1:
                $conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $conditions['Users.role_id <= '] = $this->_user['role_id'];
                $conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $conditions = null; 
            break;
        }
        $this->loadModel('Customers');
        $results = $this->Customers->find('all',[
            'contain' => ['Users','CustomerCommissions'],
            'conditions' => $conditions,
            'fields' => 'Customers.id','CustomerCommission.id'
        ]);
        foreach ($results as $customer) {
            foreach ($customer->customer_commissions as $commission) {
                $idArr[] = $commission['id'];
            }
        }

        $customerCommission = $this->CustomerCommissions->get($id, [
            'contain' => []
        ]);

        if(!in_array($customerCommission->id, $idArr)) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        }
        if ($this->request->is(['patch', 'post', 'put'])) {
            $customerCommission = $this->CustomerCommissions->patchEntity($customerCommission, $this->request->getData());
            if ($this->CustomerCommissions->save($customerCommission)) {
                $this->Flash->success(__('The customer commission has been saved.'));

                return $this->redirect(['controller' => 'Customers','action' => 'view', $this->request->getdata('customer_id')]);
            }
            $this->Flash->error(__('The customer commission could not be saved. Please, try again.'));
        }
        $this->loadModel('Departments');
        $this->loadModel('Users');
        $departments  = $this->Departments->find('all')->select(['id','name']);
        foreach ($departments as $d) {
            $tem[$d->id] = $d->name;
        }
        $departments = $tem;
        unset($tem);
        $users  = $this->Users->find('all')->select(['id','username']);
        foreach ($users as $u) {
            $tem[$u->id] = $u->username;
        }
        $users = $tem;

        $this->set(compact('customerCommission', 'departments', 'users'));
        $this->set('_serialize', ['customerCommission']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Customer Commission id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {   
        $this->request->allowMethod(['post', 'delete']);
        $customerCommission = $this->CustomerCommissions->get($id);
        if ($this->CustomerCommissions->delete($customerCommission)) {
            $this->Flash->success(__('The customer commission has been deleted.'));
        } else {
            $this->Flash->error(__('The customer commission could not be deleted. Please, try again.'));
        }

        return $this->redirect($this->referer());
    }
}
