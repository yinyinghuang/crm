<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\Filesystem\Folder;

/**
 * BusinessStatuses Controller
 *
 * @property \App\Model\Table\BusinessStatusesTable $BusinessStatuses
 */
class BusinessStatusesController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        $conditions = [];        
        switch ($this->_user['role_id']) {
            case 1:
                $conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $conditions['Users.role_id <= '] = $this->_user['role_id'];
                $conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
        }

    }



    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $this->set($_GET);
    }

    /**
     * View method
     *
     * @param string|null $id Customer Status id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $businessStatus = $this->BusinessStatuses->get($id, [
            'contain' => ['Customers', 'Users', 'Businesses']
        ]);

        $this->set('businessStatus', $businessStatus);
        $this->set('_serialize', ['businessStatus']);
    }
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        $businessStatus = $this->BusinessStatuses->newEntity();
        
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data =  $this->request->getData();

            $businessStatus = $this->BusinessStatuses->patchEntity($businessStatus,$data);
            // $businessStatus->customer_id = $this->_business->customer_id;
            $businessStatus->user_id = $this->_user['id'];

            if ($this->BusinessStatuses->save($businessStatus)) {                
                $this->loadModel('Customers')->query()
                    ->update()
                    ->set(['state' => $this->stateArr[$data['state']]])
                    ->where(['id' => $data['customer_id']])
                    ->execute();
                $this->Flash->success(__('进展添加成功.'));
                $this->updateUserLastOp($this->_user['id']);
                $this->updateCustomerModified(['id' => $data['customer_id']]);
                return $this->redirect(['controller' => 'Customers','action' => 'view',$data['customer_id']]);
            }
            $this->Flash->error(__('进展添加失败. 请重试'));
        }
        $businessStatus->next_contact_time = (new Time($businessStatus->next_contact_time))->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $this->set(compact('businessStatus'));
        $this->set('_serialize', ['businessStatus']);

    }

    /**
     * Edit method
     *
     * @param string|null $id Customer Status id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $businessStatus = $this->BusinessStatuses->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $businessStatus = $this->BusinessStatuses->patchEntity($businessStatus, $data);
            if ($this->BusinessStatuses->save($businessStatus)) {
                $this->loadModel('Customers')->query()
                    ->update()
                    ->set(['state' => $this->stateArr[$data['state']]])
                    ->where(['id' => $businessStatus->customer_id])
                    ->execute();
                $this->Flash->success(__('进展修改成功.'));
                $this->updateUserLastOp($this->_user['id']);
                $this->updateCustomerModified(['id' => $businessStatus->customer_id]);
                return $this->redirect(['controller' => 'Customers','action' => 'view',$businessStatus->customer_id]);
            }
            $this->Flash->error(__('进展修改失败. 请重试'));
        }
        $businessStatus->next_contact_time = (new Time($businessStatus->next_contact_time))->i18nFormat('yyyy-MM-dd HH:mm:ss');
        $customer = $this->loadModel('Customers')->find('all', ['conditions' => ['id' => $businessStatus->customer_id]])->first();
        $users = $this->BusinessStatuses->Users->find('list', ['limit' => 200]);
        $this->set(compact('businessStatus', 'customer', 'users', 'businesses'));
        $this->set('_serialize', ['businessStatus']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Customer Status id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {

        $this->request->allowMethod(['post', 'delete']);
        $businessStatus = $this->BusinessStatuses->get($id);
        
        if ($this->BusinessStatuses->delete($businessStatus)) {
            $folder = new Folder(WWW_ROOT.'files'.DS.'customer-images'.DS.$businessStatus->customer_id.DS.$businessStatus->business_id.DS.$id);
            $folder->delete();
            
            $this->Flash->success(__('进展删除成功.'));
        } else {
            $this->Flash->error(__('进展删除失败. 请重试.'));
        }

        return $this->redirect(['controller' => 'Customers','action' => 'view',$businessStatus->customer_id]);
    }

    public function ajaxList() 
    {

        $user_conditions = $conditions = [];
        
        switch ($this->_user['role_id']) {
            case 1:
                $user_conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $user_conditions['Users.role_id <= '] = $this->_user['role_id'];
                $user_conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
        }         

        $business_id = $_GET['business_id'];
        $business = $this->BusinessStatuses->Businesses->findById($business_id)->contain(['Users'])->where($user_conditions)->first();
        if (!$business) {
            $this->response->body('authorized_wrong');
            return $this->response;
        } 

        $conditions['Businesses.id'] = $business_id;

        if ( isset($_GET['username']) && $_GET['username'] != '' )
        {   
            $username = $_GET['username'];
            $conditions['Users.username LIKE'] = "%".$username."%";
        }

        if ( isset($_GET['startTime']) && $_GET['startTime'] != '' )
        {
            $startTime = $_GET['startTime'];
            $conditions['BusinessStatuses.modified >='] = $startTime;
        }
        if ( isset($_GET['endTime']) && $_GET['endTime'] != '' )
        {
            $endTime = $_GET['endTime'];
            $conditions['BusinessStatuses.modified <='] = $endTime;
        } 
        $order = ['BusinessStatuses.modified Desc','BusinessStatuses.id Desc']; 
        if ( isset($_GET['sort']) && $_GET['sort'] != '' && isset($_GET['direction']) && $_GET['direction'] != '' )
        {
            array_unshift($order, 'BusinessStatuses.'.$_GET['sort'] . ' '. $_GET['direction']);
        } 

        $offset = isset($_GET['page'])&& intval($_GET['page'])>0 ? ($_GET['page']-1) *20 : 0;

        $businessStatuses = $this->BusinessStatuses->find('all',[
            'contain' => ['Users','Businesses','CustomerImages'],
            'order' =>  $order,
            'limit' => '20',
            'offset' => $offset,
            'conditions' => $conditions
        ]);

        $this->set(compact('businessStatuses','username','startTime','endTime'));
        $this->set('_serialize', ['businessStatuses']);
    }

    public function done()
    {
        $this->request->allowMethod(['post', 'delete']);
        $this->BusinessStatuses->query()->update()->set(['done' => 1])->where(['id' => $this->request->getData('id')])->execute();
        $this->response->body(true);
        $this->updateUserLastOp($this->_user['id']);
        $this->updateCustomerModified(['id' => $this->_business->customer_id]);
        return $this->response;
    }
}
