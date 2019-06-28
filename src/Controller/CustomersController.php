<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\Event\Event;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\I18n\Time;
/**
 * Customers Controller
 *
 * @property \App\Model\Table\CustomersTable $Customers
 */
class CustomersController extends AppController
{    
    private $_comditions = [];


    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index($reset = null)
    {
        if (isset($_GET['submit'])){
            if($_GET['submit'] == 'export') {
                $customers =$this->filter('all');
                $this->generate_excel($customers);
            }elseif(in_array($_GET['submit'], ['sms','email','mms'])){
               
                $this->request->session()->write('Campaigns.Customers.filters',$_GET);
                return $this->redirect([
                    'controller' => 'Campaigns',
                    'action' => 'add-' . $_GET['submit']
                ]);
            }
        }
        $this->set($_GET);
        if (isset($_GET['user_id']) && $_GET['user_id']) {
            $user_name = $this->Customers->Users->findById($_GET['user_id'])->first();
            $user_name && $user_name = $user_name->username;
        }
        $eventTypes = $this->loadModel('EventTypes')->find('list');
        if (isset($_GET['event_id'])) {
            
            $event_id = array_filter(explode(',',$_GET['event_id']));
            !empty($event_id) && $event_names = $this->loadModel('Events')->find('list')->where(['id in' => $event_id]);
        }

        $this->set(compact('eventTypes','customers','user_name','event_names'));
    }




    /**
     * View method
     *
     * @param string|null $id Customer id.
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
                $conditions = null; 
            break;
        }

        $authorized = $this->Customers->findById($id)->contain(['Users'])->where($conditions)->count();
        if (!$authorized) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        } 

        $customer = $this->Customers->get($id, [
            'contain' => ['Users', 'Developers','CustomerMobiles' => function($q){
                return $q->contain(['CountryCodes'])->limit(3);
            },'CustomerEmails' => function($q){
                return $q->limit(3);
            }, 'CustomerCommissions' => function($q){
                return $q->contain(['Users' => function($query){
                    return $query->contain(['Departments']);
                }]);

            }, 'Businesses'=>function($q){
                return $q->contain(['Events','BusinessStatuses' => function($query){
                    return $query->order('BusinessStatuses.modified Desc')
                        ->contain(['Users']);
                }])->order('Businesses.modified DESC');

            }]
        ]);
        $customer->business_statuses = $this->loadModel('BusinessStatuses')
            ->find()
            ->where(['customer_id' => $id])
            ->order(['BusinessStatuses.modified DESC'])
            ->contain(['Users','CustomerImages'])
            ->limit(20)
            ->toArray();

        $customer->business_status_count = $this->BusinessStatuses
            ->find()
            ->where(['customer_id' => $id])
            ->count();
        $this->set(compact('customer','events','involved_events_arr','not_involved_events'));
        $this->set('_serialize', ['customer']);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    { 
        //若该用户有编辑客户业务员的权利，则加载出业务员列表及部门列表
        if (strpos($this->_privileges, 'e')) {
            $this->loadModel('Departments');
            $this->loadModel('Users');
            $departments  = $this->Departments->find('list');
            $users  = $this->Users->find('list')->where(['state' => 1]);
            $this->set(compact('departments', 'users'));
        }
        $sourceArr = array_filter(explode('|', $this->loadModel('Configs')->findByName('source')->first()->value));
        $customer = $this->Customers->newEntity();
        if ($this->request->is('post')) {
            if (!$this->collisionDetect('add',$customer)) {
                $data = $this->request->getData();
                $mobileArr = $emailArr = $statusArr = [];
                for ($i=1; $i < 4; $i++) { 
                    if ($this->request->getData('mobile_' .$i) && $this->request->getData('country_code_id_' .$i)) {
                        $data['customer_mobiles'][] = [
                            'country_code_id' => $data['country_code_id_' .$i],
                            'mobile' => trim($data['mobile_' .$i]),
                        ];
                    } 

                    if ($this->request->getData('email_' .$i)) {
                        $data['customer_emails'][] = [
                            'email' => $data['email_' .$i],
                        ];
                    } 

                }
                $customer = $this->Customers->patchEntity($customer, $data, ['associated' => [
                    'CustomerMobiles','CustomerEmails'
                ]]);
                isset($data['state']) && $customer['state'] = $this->stateArr[$data['state']];
                isset($data['source']) && $customer['source'] = $sourceArr[$data['source']];
                if ($this->Customers->save($customer)) {                     
                    $this->Flash->success(__('客户添加成功.'));
                    $this->updateUserLastOp($this->_user['id']);
                    if ($data['status']) {
                        $business_status_table = $this->loadModel('BusinessStatuses');
                        $business_status = $business_status_table->newEntity([
                            'customer_id' => $customer->id,
                            'status' => $data['status'],
                            'next_contact_time' => $data['next_contact_time'],
                            'user_id' =>  $this->_user['id'],
                            'next_note' => $data['next_note']
                        ]);
                        $business_status_table->save($business_status);
                    }
                    $this->updateCustomerModified(['id' => $customer->id,]);
                    $this->updateUserLastOp($this->_user['id']);
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('客户添加失败. 请重试.'));
            }else{
                for ($i=1; $i < 4; $i++) {                     
                    $customer->country_ .$i= $this->request->getData('country_code_id_'.$i);
                    $customer->mobile_ .$i= $this->request->getData('mobile_'.$i);
                    $customer->email_ .$i= $this->request->getData('email_'.$i);
                }
            }
                
        }
        $countrycodes = $this->loadModel('CountryCodes')->find('list', ['limit' => 200]);
        $sourceArr = array_filter(explode('|', $this->loadModel('Configs')->findByName('source')->first()->value));
        $this->set(compact('customer', 'sourceArr', 'countrycodes'));
        $this->set('_serialize', ['customer']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {   
        /**
         * 总监及以上有编辑的权利，也可以看/编辑全部人员的信息，所以此处不设置权限检测
         */
        
        $this->loadModel('CustomerMobiles');
        $this->loadModel('CustomerEmails');
        $departments  = $this->loadModel('Departments')->find('list');
        $users  = $this->loadModel('Users')->find('list')->where(['state' => 1]);        
        $this->set(compact('departments', 'users'));
        $sourceArr = array_filter(explode('|', $this->loadModel('Configs')->findByName('source')->first()->value));
        
        $customer = $this->Customers->get($id, [
            'contain' => ['Users', 'CustomerMobiles' => function($q){
                return $q->order(['CustomerMobiles.id' => 'ASC'])->limit(3);
            }, 'CustomerEmails' => function($q){
                return $q->order(['CustomerEmails.id' => 'ASC'])->limit(3);
            }]
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            if (!$this->collisionDetect('edit',$customer)) {
                $origin_user_id = $customer->user_id;
                $data = $this->request->getData();
                $customer = $this->Customers->patchEntity($customer, $data);
                isset($data['source']) && $customer['source'] = $sourceArr[$data['source']];
                if ($this->Customers->save($customer)) {
                    $mobile = $this->CustomerMobiles->get($this->request->getData('mobile_id_1'));
                    $mobile->country_code_id = $this->request->getData('country_code_id_1');
                    $mobile->mobile = $this->request->getData('mobile_1');
                    $this->CustomerMobiles->save($mobile);

                    for ($i=2; $i <4 ; $i++) { 
                        if ($this->request->getData('mobile_id_' . $i)) {
                             $mobile = $this->CustomerMobiles->get($this->request->getData('mobile_id_' . $i));
                             if ($this->request->getData('mobile_' . $i)) {
                                 $mobile->country_code_id = $this->request->getData('country_code_id_' . $i);
                                 $mobile->mobile = $this->request->getData('mobile_' . $i);
                                 $this->CustomerMobiles->save($mobile); 
                             }else{
                                $this->CustomerMobiles->delete($mobile);
                             }
                        }elseif ($this->request->getData('mobile_' . $i)) {
                            $mobile = $this->CustomerMobiles->newEntity();
                            $mobile->customer_id = $customer->id;
                            $mobile->country_code_id = $this->request->getData('country_code_id_' . $i);
                            $mobile->mobile = $this->request->getData('mobile_' . $i);
                            $this->CustomerMobiles->save($mobile);
                        }
                    }
                    for ($i=1; $i <3 ; $i++) { 
                        if ($this->request->getData('email_id_' . $i)) {
                             $email = $this->CustomerEmails->get($this->request->getData('email_id_' . $i));
                             if ($this->request->getData('email_' . $i)) {
                                 $email->email = $this->request->getData('email_' . $i);
                                 $this->CustomerEmails->save($email); 
                             }else{
                                $this->CustomerEmails->delete($email);
                             }
                        }elseif ($this->request->getData('email_' . $i)) {
                            $email = $this->CustomerEmails->newEntity();
                            $email->customer_id = $customer->id;
                            $email->email = $this->request->getData('email_' . $i);
                            $this->CustomerEmails->save($email);
                        }
                    }

                    if ($origin_user_id != $customer->user_id) {
                        $this->transferSql($customer->user_id,['id' => $customer->id]);
                    }                    
                     
                    $this->Flash->success(__('客户修改成功.'));
                    $this->updateUserLastOp($this->_user['id']);
                    return $this->redirect(['action' => 'index']);
                }
                $this->Flash->error(__('客户修改失败.请重试.'));
            }
        }

        
        $countrycodes = $this->CustomerMobiles->CountryCodes->find('list', ['limit' => 200]);
        $this->set(compact('customer', 'sourceArr','countrycodes'));
        $this->set('_serialize', ['customer']);
    }

    public function deleteMobile(){
        $this->request->allowMethod(['post']);
        $this->loadModel('CustomerMobiles');
        $data = null;
        $mobile = $this->CustomerMobiles->get($this->request->getData('id'));
        if ($this->CustomerMobiles->delete($mobile)) {
            $data = 1;
        } else {
            $data = 0;
        }

        $this->response->body($data);
        return $this->response;
    }


    public function deleteEmail(){
        $this->request->allowMethod(['post']);
        $this->loadModel('CustomerEmails');
        $data = null;
        $email = $this->CustomerEmails->get($this->request->getData('id'));
        if ($this->CustomerEmails->delete($email)) {
            $data = 1;
        } else {
            $data = 0;
        }

        $this->response->body($data);
        return $this->response;
    }


    /**
     * Delete method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $customer = $this->Customers->get($id);
        if ($this->Customers->delete($customer)) {
            $this->Customers->Businesses->deleteAll(['customer_id' => $id]);
            $this->Customers->BusinessStatuses->deleteAll(['customer_id' => $id]);
            $this->Customers->CustomerMobiles->deleteAll(['customer_id' => $id]);
            $this->Customers->CustomerEmails->deleteAll(['customer_id' => $id]);
            $this->Customers->CustomerImages->deleteAll(['customer_id' => $id]);

            $folder = new Folder(WWW_ROOT.'files/customer-images/'.$id);
            $folder->delete();
            $this->Flash->success(__('客户删除成功.'));
        } else {
            $this->Flash->error(__('客户删除失败. 请重试.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function ajaxList()
    {
        $customers = $this->filter();

        $this->set(compact('customers'));
    }

    /**
     * 根据条件筛选
     * @return $customers
     */
    private function filter($type = '') 
    {
        $conditions = null;
        
        switch ($this->_user['role_id']) {
            case 1:
                $conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $conditions['Users.role_id <= '] = $this->_user['role_id'];
                $conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
        } 

        if ( isset($_GET['user_id']) && $_GET['user_id'] != '' )
        {   
            $user_id = $_GET['user_id'];
            $conditions['Customers.user_id'] = $user_id;
        }

        if ( isset($_GET['name']) && $_GET['name'] != '' )
        {   
            $name = $_GET['name'];
            $conditions['Customers.name LIKE'] = "%".$name."%";
        }
        if ( isset($_GET['startTime']) && $_GET['startTime'] != '' )
        {
            $startTime = $_GET['startTime'];
            $conditions['Customers.modified >='] = $startTime;
        }
        if ( isset($_GET['endTime']) && $_GET['endTime'] != '' )
        {
            $endTime = $_GET['endTime'];
            $conditions['Customers.modified <='] = $endTime;
        } 
        if ( isset($_GET['partedStartTime']) && $_GET['partedStartTime'] != '' )
        {
            $partedStartTime = $_GET['partedStartTime'];
            $conditions['Businesses.parted >='] = $partedStartTime;
        }  
        if ( isset($_GET['partedEndTime']) && $_GET['partedEndTime'] != '' )
        {
            $partedEndTime = $_GET['partedEndTime'];
            $conditions['Businesses.parted <='] = $partedEndTime;
        }    
        if ( isset($_GET['event_id']) && $_GET['event_id'] != ',' )
        {

            $event_id = array_filter(explode(',',$_GET['event_id']));
            !empty($event_id) && $conditions['Businesses.event_id in'] = $event_id;            

        }      
        if ( isset($_GET['mobile']) && $_GET['mobile'] != '' )
        {
            $mobile = $_GET['mobile'];
            $mobiles = $this->Customers->CustomerMobiles->find()
                ->where(['mobile LIKE' => "%".$mobile."%"])
                ->extract('customer_id')
                ->toArray();
            if (empty($mobiles)) {
                return [];
            }else{
                $conditions['Customers.id in'] = $mobiles;
            }
            
        }

        if ( isset($_GET['email']) && $_GET['email'] != '' )
        {
            $email = $_GET['email'];
            $emails = $this->Customers->CustomerEmails->find()
                ->where(['email LIKE' => "%".$email."%"])
                ->extract('customer_id')
                ->toArray();
            if (empty($emails)) {
                return [];
            }else{
                 $conditions['Customers.id in'] = isset($conditions['Customers.id in']) ? array_unique(array_merge($conditions['Customers.id in'],$emails)) : $emails;
            }
            
        }
                    
        // if ( isset($_GET['state']) && !empty($_GET['state']))
        // {
        //     $conditions['Customers.state'] = $_GET['state'];
        // } 
        // if ( isset($_GET['source']) && !empty($_GET['source']))
        // {
        //     $conditions['Customers.source'] = $_GET['source'];
        // }
                    
        if ( isset($_GET['state']) && is_array($_GET['state']) && !empty($_GET['state']))
        {
            $conditions['Customers.state in'] = $_GET['state'];
        }        
        if ( isset($_GET['source']) && is_array($_GET['source']) && !empty($_GET['source']))
        {
            $conditions['Customers.source in'] = $_GET['source'];
        } 

        $order = ['Customers.modified Desc','Customers.id Desc']; 
        if ( isset($_GET['sort']) && $_GET['sort'] != '' && isset($_GET['direction']) && $_GET['direction'] != '' )
        {
            array_unshift($order, 'Customers.'.$_GET['sort'] . ' '. $_GET['direction']);
        } 
        if ($type == 'all') {
            $offset = $limit = null;
        }else{
            $limit = 20;
            $offset = isset($_GET['page'])&& intval($_GET['page'])>0 ? ($_GET['page']-1) *20 : 0;
        }

        $customers = $this->Customers->find('all',[
            'contain' => ['Users','BusinessStatuses'=>function($q){
                return $q->order('BusinessStatuses.modified Desc');
                   
            },'CustomerEmails','CustomerMobiles' => function($q){
                return $q->contain(['CountryCodes']);
            }],
            'conditions' => $conditions,
            'order' => $order ,
            'limit' => $limit,
            'offset' => $offset
        ]);
        return $customers;
    }

    /**
     * 撞客检测
     * @return [type] [description]
     */
    public function ajax()
    {   
        $this->loadModel('Users');
        $email = $mobile = $sWhere = $data = null;
        $mobile = trim($this->request->query('mobile'));
        $email = trim($this->request->query('email'));
        $country = intval($this->request->query('country')) ? intval($this->request->query('country')) : 1;

        if ($mobile != '') {
            $options = null;
            if ($this->request->query('type') == 'edit') $options = ['Customers.user_id != ' =>$this->request->query('user_id')];
            $data = $this->is_exisit_mobile($mobile, $country, $options);
        }
        if ($email != '') {
            $options = null;
            if ($this->request->query('type') == 'edit')  $options['Customers.user_id != '] = $this->request->query('user_id');
            $data = $this->is_exisit_email($email, $options);;
        }

        $this->response->body(json_encode($data));
        return $this->response;
    }


    protected function is_exisit_mobile($mobile = null, $country = null, $option =[]){
        $data = null;
        $this->loadModel('CustomerMobiles'); 
        if (!empty($option)) {
            foreach ($option as $key => $value) {
                $sWhere[$key] = $value;
            }
        }       
        $sWhere['CustomerMobiles.mobile = '] = $mobile;
        $sWhere['CustomerMobiles.country_code_id = '] = $country;
        $data = $this->CustomerMobiles->find('all',[
            'conditions' => $sWhere,
            'fields' => ['CustomerMobiles.mobile'],
            'contain' => ['Customers' => function($q){
                return $q->contain(['Users'])->select(['Customers.name','Users.username']);
            }]
        ])
        ->first();
        return $data;
    }

    protected function is_exisit_email($email = null, $option =[]){
        $data = null;
        $this->loadModel('CustomerEmails'); 
        if (!empty($option)) {
            foreach ($option as $key => $value) {
                $sWhere[$key] = $value;
            }
        }       
        $sWhere['CustomerEmails.email = '] = $email;
        $data = $this->CustomerEmails->find('all',[
            'conditions' => $sWhere,
            'fields' => ['CustomerEmails.email'],
            'contain' => ['Customers' => function($q){
                return $q->contain(['Users'])->select(['Customers.name','Users.username']);
            }]
        ])
        ->first();
        return $data;
    }


    public function import()
    {   

        if ($this->request->isPost() || $this->request->isPut()) {
            $fileOK = $this->uploadFiles(array('xls','xlsx'),'files' . DS  . 'import', $this->request->data['File'], date('YmdH',time()));
            if (array_key_exists('urls', $fileOK)) {
                $this->loadModel('CustomerMobiles');
                $this->loadModel('CustomerEmails');
                $this->loadModel('BusinessStatuses');
                $this->loadModel('Users');
                $this->loadModel('Developers');
                set_time_limit(0);
                require_once(ROOT . DS  . 'vendor' . DS  . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php');
                require_once(ROOT . DS  . 'vendor' . DS  . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel' . DS . 'IOFactory.php');

                $reader = \PHPExcel_IOFactory::load($fileOK['urls'][0]['full_url']);
                $objWorksheet = $reader->getActiveSheet()->toArray(null,true,true,true);

                if (count($objWorksheet)>2000) {
                    $this->Flash->error(__('导入不可超过2000行，请分批导入'));
                    return $this->redirect(['action' => 'import']);
                }
                // $session = $this->request->session();
                // $startRow = $session->read('import_paused_row') ? $session->read('import_paused_row') : 2;
                $this->loadModel('CountryCodes');
                $codes = $this->CountryCodes->find('all')
                    ->combine('id', 'country_code')
                    ->toArray();
                $codes = array_filter($codes);
                $developersArr = $this->Developers->find('all')
                    ->combine('id', 'name')
                    ->toArray();
                $usersArr = $this->Users->find('all')
                    ->combine('id', 'username')
                    ->toArray();
                $customerEmailsArr = $this->CustomerEmails->find('all')
                    ->combine('email', 'customer_id')
                    ->toArray();
                $customersArr = $this->Customers->find('all')
                    ->combine('id', 'name')
                    ->toArray();

                $customerMobilesArr = [];
                $query = $this->CustomerMobiles->find('all');
                    
                foreach ($query as $value) {
                    $customerMobilesArr[$value->mobile.'|'.$value->country_code_id] = $value->customer_id;
                }

                $countRow = 1;
                $datas = $data = [];
                $query_user = $this->Users->query()->insert(['username','password','state','created','modified','role_id']);
                $query_dev = $this->Developers->query()->insert(['name','state','created','modified']);
                $query_customer = $this->Customers->query()->insert(['name','user_id','developer_id','source','created','modified','remark']);
                $query_mobile = $this->CustomerMobiles->query()->insert(['customer_id','country_code_id','mobile']);
                $query_email = $this->CustomerEmails->query()->insert(['customer_id','email']);
                $newUsersArr = $newDevelopersArr = $newCustomersArr = $newMobilesArr = $newEmailsArr =  $oldCustomersArr= [];
                $customerInsert = $mobileInsert = $emailInsert = 0;
                //插入新员工以及新开发商
                $now = date('Y-m-d h:i:s');
                $password = (new DefaultPasswordHasher)->hash('123456');


                $customer_mobile_table = $this->loadModel('CustomerMobiles')->find();

                foreach ($objWorksheet as $row) {//检测表格中的错误
                    if($countRow > 1){
                        if (!$row['A'] && !$row['B'] && !$row['C'] && !$row['D'] && !$row['E'] && !$row['F'] && !$row['G'] && !$row['H'] && !$row['I'] && !$row['J']) break;
                        $data = $row;
                        $data['row'] = $countRow;
                        if ($row['A'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行客戶姓名(A' . $countRow . ')為空'));
                            return $this->redirect(['action' => 'import']);
                        }
                        
                        if ($row['I'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行業務員(I' . $countRow . ')為空'));
                            return $this->redirect(['action' => 'import']);
                        }
                        if (trim($row['G']) != '') {
                            $data['G'] = trim($row['G']);
                            if (!preg_match("/^[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[_\p{L}0-9][-_\p{L}0-9]*\.)*(?:[\p{L}0-9][-\p{L}0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/ui", $data['G'] )) {
                                    
                                $this->Flash->error(__('excel中第' . $countRow . '電郵1(G' . $countRow . ')格式不正確'));
                                return $this->redirect(['action' => 'import']);
                            }
                            if(isset($newEmailsArr[$row['G']])){
                                $this->Flash->error(__('excel中第' . $countRow . '行电邮1(G' . $countRow . ')重复'));
                                return $this->redirect(['action' => 'import']);
                            }else{
                                $newEmailsArr[$row['G']] = 1;
                            }
                        } 
                        if (trim($row['H']) != '') {
                            $data['H'] = trim($row['H']);
                            if (!preg_match("/^[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[_\p{L}0-9][-_\p{L}0-9]*\.)*(?:[\p{L}0-9][-\p{L}0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/ui", $data['H'] )) {
                                    
                                $this->Flash->error(__('excel中第' . $countRow . '電郵2(H' . $countRow . ')格式不正確'));
                                return $this->redirect(['action' => 'import']);
                            }
                            if(isset($newEmailsArr[$row['H']])){
                                $this->Flash->error(__('excel中第' . $countRow . '行电邮2(H' . $countRow . ')重复'));
                                return $this->redirect(['action' => 'import']);
                            }else{
                                $newEmailsArr[$row['H']] = 1;
                            }
                        } 
                        if ($row['C'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行電話號碼1(C' . $countRow . ')為空'));
                            return $this->redirect(['action' => 'import']);
                        } else {
                            $country_code_id = $row['B'] == '852' ? 1 : array_search($row['B'], $codes);
                            if ($country_code_id === false) {
                                $this->Flash->error(__('excel中第' . $countRow . '行區號1(B' . $countRow . ')在客戶地區中不存在或格式錯誤'));
                                return $this->redirect(['action' => 'import']);
                            } else{
                                $data['B'] = $country_code_id;
                                $data['C'] = str_replace(' ', '', $row['C']);
                                if(isset($newMobilesArr[$data['C'].'|'.$data['B']])){
                                    $this->Flash->error(__('excel中第' . $countRow . '行电话號碼1(C' . $countRow . ')重复'));
                                    return $this->redirect(['action' => 'import']);
                                }else{
                                    $newMobilesArr[$data['C'].'|'.$data['B']] = 1;
                                }
                            }                           
                        }

                        if ($row['E'] != '') {
                            $country_code_id = $row['D'] == '852' ? 1 : array_search($row['D'], $codes);
                            if ($country_code_id === false) {
                                $this->Flash->error(__('excel中第' . $countRow . '行區號2(D' . $countRow . ')在客戶地區中不存在或格式錯誤'));
                                return $this->redirect(['action' => 'import']);
                            } else{
                                $data['D'] = $country_code_id;
                                $data['E'] = str_replace(' ', '', $row['E']);
                                if(isset($newMobilesArr[$data['E'].'|'.$data['D']])){
                                    $this->Flash->error(__('excel中第' . $countRow . '行电话號碼2(E' . $countRow . ')重复'));
                                    return $this->redirect(['action' => 'import']);
                                }else{
                                    $newMobilesArr[$data['E'].'|'.$data['D']] = 1;
                                }
                            }                           
                        }



                        //檢測業務員是否存在
                        $user_id = array_search($row['I'], $usersArr);
                        if (!$user_id && !isset($newUsersArr[$row['I']])) {
                            $newUsersArr[$row['I']] = 1;                            
                            
                        } elseif($user_id){
                            $data['user_id'] = $user_id;
                        }
                        
                        //檢測開發商是否存在
                        if($row['J']){ //客户有开发商             
                            $developer_id = array_search($row['J'], $developersArr);
                            if (!$developer_id && !isset($newDevelopersArr[$row['J']])) {//开发商在系统在不存在
                                $newDevelopersArr[$row['J']] = 1;
                                 
                            } elseif($developer_id){
                                $data['developer_id'] = $developer_id;
                            }
                        } else {//没有则开发商id为0
                            $data['developer_id'] = 0;
                        }
                        $data && $datas[] = $data;
                    }
                    if ($countRow == 2001) break;
                    $countRow++;
                }

                //获取最新员工列表以及开发商列表,保存客户信息
                if (!empty($newUsersArr)) {
                    foreach ($newUsersArr as $key => $value) {
                        $query_user = $query_user->values(['username' => $key, 'password' => $password, 'state' => 1,'created' => $now, 'modified' => $now,'role_id' => 1]);
                    }
                    $query_user->execute();
                    $usersArr = $this->Users->find('list')
                        ->order('id DESC')
                        ->toArray();
                } 
                if (!empty($newDevelopersArr)) {
                    foreach ($newDevelopersArr as $key => $value) {
                        $query_dev = $query_dev->values(['name' => $key, 'state' => 1,'created' => $now, 'modified' => $now]);
                    }
                    $query_dev->execute();
                    $developersArr = $this->Developers->find('list')
                        ->order('id DESC')
                        ->toArray();
                }
                //检测客户是否为新客户
                foreach ($datas as $key => &$data) {
                    if (!isset($data['user_id'])) {//旧数据中不存在
                        $data['user_id'] = array_search($data['I'], $usersArr); //在新数据中查询
                    }
                    if ($data['J'] && !isset($data['developer_id'])) {
                        $data['developer_id'] = array_search($data['J'], $developersArr); 
                    }

                    //根据电话号码检测客户是否存在
                    $data['identify1'] = $data['C'] . '|' . $data['B'];
                    $data['identify2'] = $data['E'] . '|' . $data['D'];
                    $data['identify3'] = $data['G'];
                    $data['identify4'] = $data['H'];
                    $exist_m_c_id_1 = isset($customerMobilesArr[$data['identify1']]) ? $customerMobilesArr[$data['identify1']]:null;
                    $exist_m_c_id_2 = isset($customerMobilesArr[$data['identify2']]) ? $customerMobilesArr[$data['identify2']]:null;
                    $exist_e_c_id_1 = isset($customerEmailsArr[$data['identify3']]) ? $customerEmailsArr[$data['identify3']]:null;
                    $exist_e_c_id_2 = isset($customerEmailsArr[$data['identify4']]) ? $customerEmailsArr[$data['identify4']]:null;

                    $exist_c_id_arr = ['C' => $exist_m_c_id_1,'E' => $exist_m_c_id_2,'G' => $exist_e_c_id_1,'H' => $exist_e_c_id_2];

                    $exist_c_id_arr = array_filter($exist_c_id_arr);
                    if (empty($exist_c_id_arr)) {//本行的电话电邮均不存在与系统中，标记为新客户，插入语句。
                        $data['new'] = true;
                        $customerInsert++;
                        $query_customer = $query_customer->values(['name' => $data['A'], 'user_id' => $data['user_id'],'developer_id' => $data['developer_id'],'source' => $data['F'],'created' => $now, 'modified' => $now,'remark' => 'import']); 
                    }else{//本行的电话或者电邮不止一个存在于系统中
                        $exist_c_id_arr = array_unique($exist_c_id_arr);
                        if (count($exist_c_id_arr) !=1) {//若存在多个customer_id
                            $this->Flash->error(__('excel中第' . $data['row'] . '行中的'.implode(',', array_keys($exist_c_id_arr)).'存在于系统中，但为属于不同客户，请核实'));
                            return $this->redirect(['action' => 'import']);
                        }else{//存在统一的客户id，则标记为老客户，插入电话及电邮
                            $data['customer_id'] = array_shift($exist_c_id_arr);
                            $oldCustomersArr[] = $data['customer_id'];
                            if(!$exist_m_c_id_1) {
                                $query_mobile->values(['customer_id' => $data['customer_id'], 'country_code_id' => $data['B'], 'mobile' => $data['C']]);
                                $mobileInsert = 1;
                            }
                            if($data['E'] && !$exist_m_c_id_2) {
                                $query_mobile->values(['customer_id' => $data['customer_id'], 'country_code_id' => $data['D'], 'mobile' => $data['E']]);
                                $mobileInsert = 1;
                            }
                            if($data['G'] && !$exist_e_c_id_1) {
                                $query_email->values(['customer_id' => $data['customer_id'], 'email' => $data['G']]);
                                $emailInsert = 1;
                            }
                            if($data['H'] && !$exist_e_c_id_2) {
                                $query_email->values(['customer_id' => $data['customer_id'], 'email' => $data['H']]);
                                $emailInsert = 1;                                
                            }

                            $query = $this->Customers->get($data['customer_id']);
                            
                            if ($data['user_id'] != $query->user_id) {//若业务员不同，则转移
                                $this->transferSql($data['user_id'],['user_id' => $query->user_id]);
                            }

                            $query =  $this->Customers->patchEntity($query,['name' => $data['A'], 'user_id' => $data['user_id'],'developer_id' => $data['developer_id'],'email' => $data['E'], 'source' => $data['D']]);
                            $this->Customers->save($query);
                            $data['new'] = false;

                        }
                    }
                }
                // if(!empty($oldCustomersArr)){
                //     $this->CustomerMobiles->deleteAll(['customer_id in ' => $oldCustomersArr]);
                //     $this->CustomerEmails->deleteAll(['customer_id in ' => $oldCustomersArr]);
                // }

                if ($customerInsert) {
                    $query_customer->execute();
                    $customerIdArr = $this->Customers->find()->where(['remark' => 'import'])->order(['id DESC'])->limit($customerInsert)->extract('id')->toArray();
                }

                //添加新客户的电话及电邮
                foreach ($datas as &$data) {
                    if ($data['new']) {
                        $data['customer_id'] = array_pop($customerIdArr);
                        $mobileInsert = 1;
                        $query_mobile->values(['customer_id' => $data['customer_id'], 'country_code_id' => $data['B'], 'mobile' => $data['C']]);
                        if($data['E']) {
                            $query_mobile->values(['customer_id' => $data['customer_id'], 'country_code_id' => $data['D'], 'mobile' => $data['E']]);
                        }
                        if($data['G']) {
                            $query_email->values(['customer_id' => $data['customer_id'], 'email' => $data['G']]);
                            $emailInsert = 1;
                        }
                        if($data['H']) {
                            $query_email->values(['customer_id' => $data['customer_id'], 'email' => $data['H']]);
                            $emailInsert = 1;                                
                        }

                    }
                }

                if($mobileInsert) $query_mobile->execute();
                if($emailInsert) $query_email->execute();
                $this->updateUserLastOp($this->_user['id']);
                $this->Flash->success(__('成功导入资料.'));
                return $this->redirect(['action' => 'index']);
            } 
            $this->Flash->error(__('未能成功导入资料.'));
            $this->set(compact('customer'));
        }
        
    }

    protected function collisionDetect($type,$customer)
    {
        $option = $type === 'edit' ? ['Customers.user_id != ' => $customer->user_id] : [];

        $isExistMobileArr = array();
        for ($i=0; $i < 3; $i++) { 
            $isExistMobile = null;
            $mobile = trim($this->request->getData('mobile_'.$i));
            $code = $this->request->getData('country_code_id_'.$i);
            $mobile && $code && $isExistMobile = $this->is_exisit_mobile($mobile, $code,$option);
            if($isExistMobile != null) $isExistMobileArr[] = $isExistMobile;
        }

        $isExistEmailArr = array();
        for ($i=0; $i < 3; $i++) { 
            $isExistEmail = null;
            $email = trim($this->request->getData('email_'.$i));
            $email && $isExistEmail = $this->is_exisit_email($email,$option);
            if($isExistEmail != null) $isExistEmailArr[] = $isExistEmail;
        }

        if (!empty($isExistMobileArr) || !empty($isExistEmailArr)) {
            if ($isExistMobileArr) {
                foreach ($isExistMobileArr as $k => $v) {
                    $this->Flash->error(__($v->customer->name.'(電話：'.$v->mobile . ')現在是' . $v->customer->user->username . '的客戶,請勿重復添加'));
                }
                
            }
            if ($isExistEmailArr) {
                foreach ($isExistEmailArr as $k => $v) {
                    $this->Flash->error(__($v->customer->name.'(電郵：'.$v->email . ')現在是' . $v->customer->user->username . '的客戶,請勿重復添加'));
                }
                
            }
            return true;
        }else {
            return false;
        }
    }

    public function autocompelete(){

        $conditions = $customerArr = $data = [];

        switch ($this->_user['role_id']) {
            case 1:
                $conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $conditions['Users.role_id <= '] = $this->_user['role_id'];
                $conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
        }

        $event_id = $this->request->query('event_id');
        if ($event_id) {
            $custmer_involved = $this->Customers->Businesses
                ->find()
                ->contain(['Users'])
                ->where(['event_id' => $event_id] + $conditions)
                ->extract('customer_id')
                ->toArray();
            !empty($custmer_involved) && $conditions['Customers.id not in'] = $custmer_involved;
        }
        
        $name = $this->request->query('query');
        $name && $conditions['name LIKE '] = '%' . $name . '%';

        $query = $this->Customers->find('all',[
            'conditions' => $conditions,
            'contain' => ['Users','CustomerMobiles' => function($q){
                return $q->contain(['CountryCodes']);
            }],
            'order' => ['Customers.modified','Customers.id']
        ]);
        foreach ($query as $customer) {

            $mobile = $customer->customer_mobiles[0];
            $dataArr = [];
            $dataArr['value'] = $customer->name .'(+'.$mobile['country_code']['country_code'].'-'.$mobile['mobile'].')';
            $dataArr['data'] = ['customer_id' => $customer->id,'user_id' => $customer->user_id];
            $customerArr[] = $dataArr;
        }
        $data = [
            "query" => "Unit",
            "suggestions" => $customerArr,
        ];
        $this->response->body(json_encode($data));
        return $this->response;
    }

    public function transfer()
    {
        $eventTypes = $this->loadModel('EventTypes')->find('list');
        $this->set(compact('eventTypes'));
    }

    public function transferEntire()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        $to_user_id = $request['to_user_id'];
        $from_user_id = array_filter(explode(',', $request['from_user_id']));

        if(empty($from_user_id)){
            $this->Flash->error(__('转出的业务员不能为空.'));
        }else if($to_user_id == ''){
            $this->Flash->error(__('转入的业务员不能为空.'));
        }else{
            $this->transferSql($to_user_id,['user_id in' => $from_user_id]);
            $this->Flash->success(__('客户转移成功.'));
            return $this->redirect(['action' => 'index']);
        }
        return $this->redirect(['action' => 'transfer']);
    }


    public function transferFilter()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        

        $conditions = [];

        if ( isset($request['name']) && $request['name'] != '' )
        {   
            $name = $request['name'];
            $conditions['Customers.name LIKE'] = "%".$name."%";
        }

        if ( isset($request['user_id']) && $request['user_id'] != '' )
        {   
            $user_id = $request['user_id'];
            $conditions['Customers.user_id'] = $user_id;
        }

        if ( isset($request['source']) && $request['source'] != '' )
        {
            $source = $request['source'];
            $conditions['Customers.source LIKE'] = "%".$source."%";
        }
        if ( isset($request['email']) && $request['email'] != '' )
        {
            $email = $request['email'];
            $conditions['Customers.email LIKE'] = "%".$email."%";
        }
        if ( isset($request['startTime']) && $request['startTime'] != '' )
        {
            $startTime = $request['startTime'];
            $conditions['Customers.modified >='] = $startTime;
        }
        if ( isset($request['endTime']) && $request['endTime'] != '' )
        {
            $endTime = $request['endTime'];
            $conditions['Customers.modified <='] = $endTime;
        }        
        if ( isset($request['mobile']) && $request['mobile'] != '' )
        {
            $mobile = $request['mobile'];
            $conditions['Customers.id in'] = $this->Customers->CustomerMobiles->find()
                ->where(['mobile LIKE' => "%".$mobile."%"])
                ->extract('customer_id')
                ->toArray();
        }

        if(empty($conditions)){
            $this->Flash->error(__('至少填写一个条件.'));
        }elseif(!isset($request['to_user_id_filter']) || $request['to_user_id_filter'] == ''){
            $this->Flash->error(__('转入的业务员不能为空.'));
        }else{
            $this->transferSql($request['to_user_id_filter'],$conditions);
            $this->Flash->success(__('客户转移成功.'));
            return $this->redirect(['action' => 'index']);
        }
        return $this->redirect(['action' => 'transfer']);
    }

    public function bulk()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        if(!empty($request['ids'])){
            switch ($request['submit']) {
                case 'del':
                    $this->Customers->deleteAll(['id in' => $request['ids']]);
                    $this->Customers->Businesses->deleteAll(['customer_id in' => $request['ids']]);
                    $this->Customers->BusinessStatuses->deleteAll(['customer_id in' => $request['ids']]);
                    $this->Customers->CustomerMobiles->deleteAll(['customer_id in' => $request['ids']]);
                    $this->Customers->CustomerEmails->deleteAll(['customer_id in' => $request['ids']]);
                    $this->Customers->CustomerImages->deleteAll(['customer_id in' => $request['ids']]);
                    foreach ($request['ids'] as $id) {
                        $folder = new Folder(WWW_ROOT.'files/customer-images/'.$id);
                        $folder->delete();
                    }
                    $this->Flash->success(__('删除成功.'));
                    break;
                case 'export':
                    $conditions = ['Customers.id in' => $request['ids']];

                    $customers = $this->Customers->find()
                        ->contain(['Users','CustomerMobiles','CustomerEmails','Developers'])
                        ->where($conditions);


                    $this->generate_excel($customers);
                    break;
                case 'transfer':
                    if($request['to_user_id'] == ''){
                        $this->Flash->error(__('转入的业务员不能为空.'));
                    }else{
                        $this->transferSql($request['to_user_id'],['Customers.id in' => $request['ids']]);
                        $this->Flash->success(__('客户转移成功.'));
                    }
                    break;
                case 'status':
                    $now = (new Time())->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $query = $this->loadModel('BusinessStatuses')->query()->insert(['customer_id','status','next_contact_time','next_note','user_id','created','modified','done']);
                    if ($request['status'] || $request['next_contact_time']) {                        
                        foreach ($request['ids'] as $customer_id) {
                            $query->values(['customer_id' =>$customer_id,'status' =>$request['status'],'next_contact_time' =>$request['next_contact_time'],'next_note' =>$request['next_note'],'user_id' =>$this->_user['id'],'created' =>$now,'modified' =>$now,'done' =>0]);
                        }
                        $query->execute();
                        $this->updateCustomerModified(['id in' => $request['ids']]);
                        $this->updateUserLastOp($this->_user['id']);
                        $this->Flash->success(__('批量更新成功.'));
                    }
                    break;
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }



    private function transferSql($to_user_id,$conditions){
        $customers_id = $this->Customers->find()->where($conditions)->extract('id')->toArray();
        if (!empty($customers_id)) {
            $this->Customers->query()
                ->update()
                ->set(['user_id' => $to_user_id])
                ->contain(['Businesses'])
                ->where($conditions)
                ->execute();
            $this->loadModel('Businesses')->query()
                ->update()
                ->set(['user_id' => $to_user_id])
                ->where(['customer_id in' => $customers_id])
                ->execute();
            $this->loadModel('BusinessStatuses')->query()
                ->update()
                ->set(['user_id' => $to_user_id])
                ->where(['customer_id in' => $customers_id])
                ->execute();
        }
    }
    public function export()
    {
        $customers = $this->Customers->find('all',[
            'contain' => ['Users','BusinessStatuses'=>function($q){
                return $q->order('BusinessStatuses.modified Desc')->group('BusinessStatuses.customer_id');
                   
            },'CustomerEmails','CustomerMobiles' => function($q){
                return $q->contain(['CountryCodes']);
            },'Developers']
        ]);

        $this->generate_excel($customers);
    }

    private function generate_excel($customers){
        set_time_limit(0);
        $codes = $this->loadModel('CountryCodes')->find()->combine('id','country_code')->toArray();

        /** 引入PHPExcel */
        
        require_once(ROOT . DS  . 'vendor' . DS  . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php');
        require_once(ROOT . DS  . 'vendor' . DS  . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel' . DS . 'IOFactory.php');
        // 创建Excel文件对象
        $objPHPExcel = new \PHPExcel();
        // 设置文档信息，这个文档信息windows系统可以右键文件属性查看
        $objPHPExcel->getProperties()->setCreator("")
            ->setLastModifiedBy("")
            ->setTitle("export_customers")
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");


        //根据excel坐标，添加数据
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '编号')
            ->setCellValue('B1', '姓名')
            ->setCellValue('C1', '电话区号')
            ->setCellValue('D1', '电话(必填)')
            ->setCellValue('E1', '电话区号')
            ->setCellValue('F1', '电话')
            ->setCellValue('G1', '来源')
            ->setCellValue('H1', '电邮1')
            ->setCellValue('I1', '电邮2')
            ->setCellValue('J1', '业务员')
            ->setCellValue('K1', '开发商');

        $row = 2;

        foreach ($customers as $customer) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $customer['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $customer['name']);
            if(isset($customer['customer_mobiles'][0]['country_code_id'])){
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$row, $codes[$customer['customer_mobiles'][0]['country_code_id']]);
            }
            if(isset($customer['customer_mobiles'][0]['mobile'])){
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$row, $customer['customer_mobiles'][0]['mobile']);
            }
            if(isset($customer['customer_mobiles'][1]['country_code_id'])){
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$row, $codes[$customer['customer_mobiles'][1]['country_code_id']]);
            }
            if(isset($customer['customer_mobiles'][1]['mobile'])){
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$row, $customer['customer_mobiles'][1]['mobile']);
            }
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$row, $customer['source']);
            if(isset($customer['customer_emails'][0]['email'])){
                $objPHPExcel->getActiveSheet()->setCellValue('H'.$row,$customer['customer_emails'][0]['email']);
            }
            if(isset($customer['customer_emails'][1]['email'])){
                $objPHPExcel->getActiveSheet()->setCellValue('I'.$row,$customer['customer_emails'][1]['email']);
            }
            if(isset($customer['user']['username'])){
                $objPHPExcel->getActiveSheet()->setCellValue('J'.$row,$customer['user']['username']);
            }
            if(isset($customer['developer']['name'])){
                $objPHPExcel->getActiveSheet()->setCellValue('K'.$row,$customer['developer']['name']);
            }
            $row++;
        }

        // 重命名工作sheet
        $objPHPExcel->getActiveSheet()->setTitle('sheet');

        // 设置第一个sheet为工作的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"export_customers.xlsx\"");
        header('Cache-Control: max-age=0');
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }
}
