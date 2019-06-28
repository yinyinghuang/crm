<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Date;
use Cake\I18n\Time;

/**
 * Events Controller
 *
 * @property \App\Model\Table\EventsTable $Events
 */
class EventsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $orderOptions = ['报名最多','报名最少','成交最多','成交最少','失败最多','失败最少','进行中最多','进行中最少'];
        $eventTypes = $this->Events->EventTypes->find('list');
        $this->set(compact('orderOptions','eventTypes'));
        $this->set($_GET);
    }

    /**
     * View method
     *
     * @param string|null $id Event id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
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
                $conditions = []; 
            break;
        }


        $event = $this->Events->get($id, [
            'contain' => ['EventTypes', 'Businesses' => function ($q)
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
                        $conditions = []; 
                    break;
                }
                
                return $q->contain(['Users','Customers','BusinessStatuses' => function($query)
                {
                    return $query->order('BusinessStatuses.modified Desc')
                        ->limit(1);
                }])->where($conditions)->limit(20);
            }]
        ]);

        $yestoday = date("Y-m-d",strtotime("-1 day")) . ' 00:00:00';
        $today = date("Y-m-d") . ' 00:00:00';
        $businesses = $this->loadModel('Businesses');
        $event->new_customer_count = $businesses->find()
            ->where(['Businesses.created >=' => $yestoday,'Businesses.created <=' => $today,'event_id' => $id]+$conditions)
            ->contain(['Users'])
            ->count();

        $event->total = $businesses
            ->find()
            ->where(['event_id' => $event->id]+$conditions)
            ->contain(['Users'])
            ->count();
        $event->ing = $businesses
            ->find()
            ->where(['event_id' => $event->id,'Businesses.state' => 0]+$conditions)
            ->contain(['Users'])
            ->count();
        $event->closed = $businesses
            ->find()
            ->where(['event_id' => $event->id,'Businesses.state' => 1]+$conditions)
            ->contain(['Users'])
            ->count();
        $event->signed = $businesses
            ->find()
            ->where(['event_id' => $event->id,'Businesses.state' => 2]+$conditions)
            ->contain(['Users'])
            ->count();

        $stateArr = ['进行中','失败','成交'];
        $this->set(compact('event','stateArr','related_number'));
        $this->set('_serialize', ['event']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $event = $this->Events->newEntity();
        if ($this->request->is('post')) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            $this->request->getData('start_time') && $event->start_time =  $this->request->getData('start_time'). ' 00:00:00'; 
            $this->request->getData('end_time') && $event->end_time =  $this->request->getData('end_time'). ' 00:00:00'; 
            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $eventTypes = $this->Events->EventTypes->find('list');
        $this->set(compact('event', 'eventTypes'));
        $this->set('_serialize', ['event']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Event id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $event = $this->Events->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $event = $this->Events->patchEntity($event, $this->request->getData());
            
            $this->request->getData('start_time') && $event->start_time = $this->request->getData('start_time') . ' 00:00:00';
            $this->request->getData('end_time') && $event->end_time = $this->request->getData('end_time') . ' 23:59:59';

            if ($this->Events->save($event)) {
                $this->Flash->success(__('The event has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The event could not be saved. Please, try again.'));
        }
        $event->start_time && $event->start_time = (new Time($event->start_time))->i18nFormat('yyyy-MM-dd');
        $event->end_time && $event->end_time = (new Time($event->end_time))->i18nFormat('yyyy-MM-dd');
        $eventTypes = $this->Events->EventTypes->find('list');
        $this->set(compact('event', 'eventTypes'));
        $this->set('_serialize', ['event']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Event id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $event = $this->Events->get($id);
        $business_count =  $this->Events->Businesses->find()
            ->where(['event_id' => $id])
            ->count();
        if ($business_count) {
            $this->Flash->error(__('该活动下有客户活动记录，无法删除'));
        } else {
            if ($this->Events->delete($event)) {
                $this->Flash->success(__('删除成功'));
            } else {
                $this->Flash->error(__('删除失败. 请重试.'));
            }
        }
        

        return $this->redirect(['action' => 'index']);
    }

    public function ajaxList() 
    {

        $orders = [[null,'DESC'],[null,'ASC'],[2,'DESC'],[2,'ASC'],[1,'DESC'],[1,'ASC'],[0,'DESC'],[0,'ASC']];
        $order = ['Events.modified Desc','Events.id Desc'];

        $conditions = null;
        switch ($this->_user['role_id']) {
            case 1:
                $user_conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $user_conditions['Users.role_id <= '] = $this->_user['role_id'];
                $user_conditions['Users.department_id'] = $this->_user['department_id']; 
            case 3:
            case 4:
                $user_conditions=[];
            break;
        }

        if ( isset($_GET['name']) && $_GET['name'] != '' )
        {   
            $name = $_GET['name'];
            $conditions['Events.name LIKE'] = "%".$name."%";
        }
        if ( isset($_GET['state']) && $_GET['state'] != '' )
        {
            $state = $_GET['state'];
            $conditions['Events.state ='] = $state;
        }
        if ( isset($_GET['startTime']) && $_GET['startTime'] != '' )
        {
            $startTime = $_GET['startTime'];
            $conditions['Events.start_time >='] = $startTime;
        }
        if ( isset($_GET['endTime']) && $_GET['endTime'] != '' )
        {
            $endTime = $_GET['endTime'];
            $conditions['Events.end_time <='] = $endTime;
        } 

        if ( isset($_GET['event_type_id']) && $_GET['event_type_id'] !== '' )
        {
            $event_type_id = $_GET['event_type_id'];
            $conditions['Events.event_type_id ='] = $event_type_id;
        }

        if ( isset($_GET['order']) && $_GET['order'] !== '' )
        {
            $orderIndex = intval($_GET['order']);
            $orderArr = $orders[$orderIndex];
            array_unshift($order, 'total_businesses '.$orderArr[1]);
        }

        if ( isset($_GET['sort']) && $_GET['sort'] != '' && isset($_GET['direction']) && $_GET['direction'] != '' )
        {
            array_unshift($order, 'Events.'.$_GET['sort'] . ' '. $_GET['direction']);
        } 

        $offset = isset($_GET['page'])&& intval($_GET['page'])>0 ? ($_GET['page']-1) *20 : 0;

        $events = $this->Events->find();            
        $events
            ->select(['total_businesses' => $events->func()->count('Businesses.id')])
            ->leftJoinWith('Businesses', function ($q) {

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
                if ($conditions) {
                    $userTable = $this->loadModel('Users');
                    $users = $userTable->find()->where($conditions)->extract('id')->toArray();
                    unset($conditions);
                    $conditions['Businesses.user_id in'] = $users;
                }
                
                if ( isset($_GET['order']) && $_GET['order'] != '')
                {
                    $orders = [[null,'DESC'],[null,'ASC'],[2,'DESC'],[2,'ASC'],[1,'DESC'],[1,'ASC'],[0,'DESC'],[0,'ASC']];
                    $orderIndex = intval($_GET['order']);
                    $orderArr = $orders[$orderIndex];
                    $orderArr[0]!==null && $conditions['Businesses.state'] = $orderArr[0];
                }

                return $q->where($conditions);
            })
            ->contain(['EventTypes'])
            ->where($conditions)
            ->limit(20)
            ->offset($offset)
            ->group(['Events.id'])
            ->order($order)
            ->enableAutoFields(true);

        $businesses = $this->loadModel('Businesses');
        foreach ($events as $event) {
            $event->total = $businesses
                ->find()
                ->where(['event_id' => $event->id]+$user_conditions)
                ->contain(['Users'])
                ->count();
            $event->ing = $businesses
                ->find()
                ->where(['event_id' => $event->id,'Businesses.state' => 0]+$user_conditions)
                ->contain(['Users'])
                ->count();
            $event->closed = $businesses
                ->find()
                ->where(['event_id' => $event->id,'Businesses.state' => 1]+$user_conditions)
                ->contain(['Users'])
                ->count();
            $event->signed = $businesses
                ->find()
                ->where(['event_id' => $event->id,'Businesses.state' => 2]+$user_conditions)
                ->contain(['Users'])
                ->count();
        }

        $this->set(compact('events','name','startTime','endTime','state','event_type_id'));
        $this->set('_serialize', ['events']);
    }

    public function autocompelete(){
        $conditions = $eventArr = $data = [];
        
        $name = $this->request->query('query');
        $name && $conditions['name LIKE '] = '%' . $name . '%';

        $customer_id = $this->request->query('customer_id');
        $non_event_id = $this->request->query('non_event_id');

        if ($non_event_id &&  array_filter(explode(',', $non_event_id))) {
            $conditions['Events.id not in'] = array_filter(explode(',', $non_event_id));
        }

        if ($customer_id) {
            $event_involved = $this->Events->Businesses
                ->find()
                ->where(['customer_id' => $customer_id])
                ->extract('event_id')
                ->toArray();
            !empty($event_involved) && $conditions['Events.id not in'] = $event_involved;
        }

        $query = $this->Events->find('all',[
            'conditions' => $conditions,
            'fields' => ['Events.id','Events.name','Events.event_type_id']
        ]);
        foreach ($query as $event) {
            $dataArr = [];
            $dataArr['value'] = $event->name;
            $dataArr['data'] = ['event_id' => $event->id,'event_type_id' => $event->event_type_id];
            $eventArr[] = $dataArr;
        }
        $data = [
            "query" => "Unit",
            "suggestions" => $eventArr,
        ];
        $this->response->body(json_encode($data));
        return $this->response;
    }

    public function campaign($type)
    {    
        $this->request->session()->write('Campaigns.Customers.filters',$this->request->query());
        return $this->redirect([
            'controller' => 'Campaigns',
            'action' => 'add-' . $type
        ]);
    }
    public function bulk()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        if(!empty($request['ids'])){
            switch ($request['submit']) {
                case 'del':
                    $this->Events->deleteAll(['id in' => $request['ids']]);
                    $business_ids = $this->Events->Businesses->find()->where(['event_id in' => $request['ids']])->extract('id')->toArray();
                    $this->Events->Businesses->deleteAll(['event_id in' => $request['ids']]);
                    !empty($business_ids) && $this->Events->Businesses->BusinessStatuses->deleteAll(['business_id in' => $business_ids]);
                    $this->Flash->success(__('删除成功.'));
                    break;
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }



}
