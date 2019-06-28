<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Departments Controller
 *
 * @property \App\Model\Table\DepartmentsTable $Departments
 */
class DepartmentsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {   
        $conditions = [];
        switch ($this->_user['role_id']) {
            case 2:
                $conditions['id'] = $this->_user['department_id']; 
            break;
        }
        $this->paginate = [
            'conditions' => $conditions
        ];
        $this->loadModel('Customers');
        $this->loadModel('Businesses');
        $this->loadModel('Users');
        $departments = $this->Departments->find('all')
            ->listNested();
        $this->set(compact('departments'));
        $this->set('_serialize', ['departments']);
    }

    /**
     * View method
     *
     * @param string|null $id Department id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {

        switch ($this->_user['role_id']) {
            case 2:
                $conditions['id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $conditions = null; 
            break;
        }

        $authorized = $this->Departments->findById($id)->where($conditions)->count();
        if (!$authorized) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        } 

        $department = $this->Departments->get($id, [
            'contain' => ['Users' => function($q){
                return $q->contain(['Roles']);
            }]
        ]);

        $this->loadModel('Customers');
        $this->loadModel('Businesses');
        $this->loadModel('Users');
        $department->user_count = $this->Users->find()
            ->where(['Users.department_id' => $department->id])
            ->count();
        $department->customer_count = $this->Customers->find()
            ->where(['Users.department_id' => $department->id])
            ->contain(['Users'])
            ->count();
        $department->business_count = $this->Businesses->find()
            ->where(['Users.department_id' => $department->id])
            ->contain(['Users'])
            ->count();

        foreach ($department->users as $user) {            
            $user->customer_count = $this->Customers->find()
                ->where(['Customers.user_id' => $user->id])
                ->count();
            $user->business_count = $this->Businesses->find()
                ->where(['Businesses.user_id' => $user->id])
                ->count(); 
        }

        $this->set('department', $department);
        $this->set('_serialize', ['department']);

    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function sync()
    {
        $this->loadComponent('CorpWechat');
        $res = $this->CorpWechat->departmentList(1);
        $ids = [];
        if($res['errcode']==0){
            foreach ($res['department'] as $department) {
                $entity = $this->Departments->findById($department['id'])->first()?:
                    $this->Departments->newEntity();
                $entity = $this->Departments->patchEntity($entity,$department);
                $entity->sort = $department['order'];
                $this->Departments->save($entity);
                $ids[] = $entity->id;                
            }
            $this->Departments->deleteAll(['id not in'=>$ids]);
        }
        return $this->redirect(['action' => 'index']);
        // $this->Departments->deleteAll([1=>1]);
    }

    /**
     * Edit method
     *
     * @param string|null $id Department id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {

        $department = $this->Departments->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $department = $this->Departments->patchEntity($department, $this->request->getData());
            if ($this->Departments->save($department)) {
                $this->Flash->success(__('The department has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The department could not be saved. Please, try again.'));
        }
        $this->set(compact('department'));
        $this->set('_serialize', ['department']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Department id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $department = $this->Departments->get($id);
        if ($this->Departments->delete($department)) {
            $this->Flash->success(__('The department has been deleted.'));
        } else {
            $this->Flash->error(__('The department could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
}
