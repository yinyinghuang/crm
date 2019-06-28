<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Database\Schema\TableSchema;

/**
 * Users Controller
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        if (isset($_GET['submit'])){
            if($_GET['submit'] == 'export') {
                $users = $this->filter('all');
                $this->export($users);
            }
        }

        $userStateArr = [0 => '离职',1 => '在职'];
        $departments = $this->Users->Departments->find('list');
        $this->set(compact('users','userStateArr','departments'));
        $this->set($_GET);
    }

    /**
     * View method
     *
     * @param string|null $id User id.
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
                $conditions['role_id <= '] = $this->_user['role_id'];
                $conditions['department_id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $conditions = null; 
            break;
        }

        $authorized = $this->Users->findById($id)->where($conditions)->count();
        if (!$authorized) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        } 

        $user = $this->Users->get($id, [
            'contain' => ['Departments', 'Roles', 'CustomerCommissions' => function($q){
                return $q->contain(['Customers'])->order(['CustomerCommissions.id' => 'Desc'])->limit(50);
            }, 'Businesses' => function($q){
                return $q->where(['Businesses.state' => 0])->contain(['Customers','Events'])->order(['Businesses.modified' => 'Desc'])->limit(20);
            }, 'Customers' => function($q){
                return $q->contain(['Developers','BusinessStatuses','CustomerMobiles' => function($q){
                    return $q->contain(['CountryCodes']);
                }])->order(['Customers.modified' => 'Desc'])->limit(20);
            },'CountryCodes']
        ]);

        $customer_table = $this->Users->Customers->find();

        $this->_businessData = $this->_customerData = [];
        $week = date('W');
        $week_lately_arr = [$week-3,$week-2,$week-1,$week-0];
        $customerData = $customer_table
            ->select(['week' => 'WEEK(created)+1','total' => 'count(id)'])
            ->where(['created >=' => new Date('-4 weeks'),'user_id' => $id])
            ->group(['week'])
            ->order('week ASC');
        foreach ($customerData as $value) {
            $this->_customerData[$value->week]['total'] = $value->total;
        }

        $business_table = $this->Users->Businesses->find();
        $businessSingedOrClosedData = $business_table
            ->select([
                'week' => 'WEEK(created)+1',
                'signed' => 'SUM(CASE WHEN Businesses.state=2 THEN 1 else 0 END)',
                'closed' => 'SUM(CASE WHEN Businesses.state=1 THEN 1 else 0 END)'
            ])
            ->where(['modified >=' => new Date('-4 weeks'),'user_id' => $id])
            ->group(['week'])
            ->order('week ASC');
        foreach ($businessSingedOrClosedData as $value) {
            $this->_businessData[$value->week]['signed'] = $value->signed;
            $this->_businessData[$value->week]['closed'] = $value->closed;
        }

        $businessTotalData = $business_table
            ->select([
                'week' => 'WEEK(created)+1',
                'total' => 'SUM(1)'
            ])
            ->where(['created >=' => new Date('-4 weeks'),'user_id' => $id])
            ->group(['week'])
            ->order('week ASC');

        foreach ($businessTotalData as $value) {
            $this->_businessData[$value->week]['total'] = $value->total;
        }

        $labelArr = ['total' => ['新增总数','black'],'signed' => ['新增成交','green'],'closed' => ['新增失败','red']];
        $count_customer = $this->Users->Customers->find()->where(['user_id' => $id])->count();
        $count_business = $this->Users->Businesses->find()->where(['user_id' => $id,'state' => 0])->count();
        $this->set(compact('user','count_customer','labelArr','week_lately_arr','count_business'));
        $this->set('_serialize', ['user']);
        $this->set('businessData', $this->_businessData);
        $this->set('customerData', $this->_customerData);
    }

    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {

        switch ($this->_user['role_id']) {
            case 2:
                $conditions_d['id'] = $this->_user['department_id']; 
                $conditions_r['id <= '] = $this->_user['role_id']; 
            break;
            case 3:
            case 4:
                $conditions_d = $conditions_r = null; 
            break;
        }

        $user = $this->Users->newEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('员工添加成功.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('员工添加失败. 请重试.'));
        }
        $departments = $this->Users->Departments->find('list', ['limit' => 200,'conditions' => $conditions_d]);
        $roles = $this->Users->Roles->find('list', ['limit' => 200,'conditions' => $conditions_r]);
        $countrycodes = $this->Users->CountryCodes->find('list', ['limit' => 200]);
        $this->set(compact('user', 'departments', 'roles','countrycodes'));
        $this->set('_serialize', ['user']);
    }

    public function sync()
    {

        $this->loadComponent('CorpWechat');
        $departments = $this->loadModel('Departments')->find('list');
        $password = (new DefaultPasswordHasher)->hash('123456'); 
        $ids = [];  
        foreach ($departments as $department_id => $value) {
            $res = $this->CorpWechat->userList($department_id);           
            if($res['errcode']==0){ 
                foreach ($res['userlist'] as $user) {
                    if(in_array($user['userid'], $ids)) continue;
                    $new_depts = $user['department'];
                    unset($user['department']);
                    $u = $this->Users->findByUserid($user['userid'])->first()?:
                        $this->Users->newEntity();
                    $u = $this->Users->patchEntity($u,$user);
                    if($u->isNew()){
                        $u->password = $password;
                    }
                    $this->Users->save($u);
                    $ids[] = $u->userid;
                    $user_id =  $u->id;
                    //保存所属部门  
                    $user_dept_table = $this->loadModel('UserDepartments');
                    $origin_depts = $user_dept_table->findByUserId($u->id)->extract('department_id')->toArray(); 
                    $add_depts = array_diff($new_depts, $origin_depts);
                    $delete_depts = array_diff($origin_depts, $new_depts);
                    if(count($add_depts)){
                        $datas=[];
                        foreach ($add_depts as $department_id) {
                            $datas[] = compact('user_id','department_id');
                        }
                        $entities = $user_dept_table->newEntities($datas);                        
                        $user_dept_table->saveMany($entities);
                    } 
                    if(count($delete_depts)){
                        $user_dept_table->deleteAll(['department_id in' => $delete_depts,'user_id' => $user_id]);
                    }
                }
                
            }

        }
        if(!empty($ids)) $this->Users->deleteAll(['userid not in'=>$ids]);
        return $this->redirect(['action' => 'index']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {

        switch ($this->_user['role_id']) {
            case 2:
                $conditions['role_id <= '] = $this->_user['role_id'];
                $conditions['department_id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $conditions = null; 
            break;
        }

        $authorized = $this->Users->findById($id)->where($conditions)->count();
        if (!$authorized) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        } 

        
        $user = $this->Users->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('员工修改成功.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('员工修改失败. 请重试.'));
        }
        $departments = $this->Users->Departments->find('list', ['limit' => 200]);
        $roles = $this->Users->Roles->find('list', ['limit' => 200]);
        $countrycodes = $this->Users->CountryCodes->find('list', ['limit' => 200]);
        $this->set(compact('user', 'departments', 'roles','countrycodes'));
        $this->set('_serialize', ['user']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('员工删除成功.'));
        } else {
            $this->Flash->error(__('员工删除失败. 请重试.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function login()
    {
        if ($this->request->is('post')) {
            $user = $this->Auth->identify();


            if ($user) {
                if ((new Time($user['last_login']))->i18nFormat('yyyy-MM-dd') !== date('Y-m-d')) {
                    $u = $this->Users->get($user['id']);
                    $u->last_login = date('Y-m-d');
                    $this->Users->save($u);
                    $user['first_login'] = true;
                }else{
                    $user['first_login'] = false;  
                }
                

                $this->Auth->setUser($user);
                return $this->redirect($this->Auth->redirectUrl());
            }
            $this->Flash->error(__('用戶名稱或密碼不正確, 請重試!'));
        }
    }

    public function logout()
    {
        return $this->redirect($this->Auth->logout());
    }
    public function ajaxList()
    {
        $users = $this->filter();
        $userStateArr = [0 => '离职',1 => '在职'];
        $this->set(compact('users','userStateArr'));
        $this->set('_serialize', ['users']);
    }
    private function filter($type = '') 
    {
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

        if ( isset($_GET['name']) && $_GET['name'] != '' )
        {   
            $name = $_GET['name'];
            $conditions['Users.name LIKE'] = "%".$name."%";
        }
        if ( isset($_GET['status']) && !empty($_GET['status']))
        {
            $status = $_GET['status'];
            $conditions['Users.status in'] = $state;
        }
        if ( isset($_GET['mobile']) && $_GET['mobile'] != '' )
        {
            $mobile = $_GET['mobile'];
            $conditions['Users.mobile LIKE'] = "%".$mobile."%";
        }
        if ( isset($_GET['department_id']) && $_GET['department_id'] != '' )
        {
            $department_id = $_GET['department_id'];
            $conditions['Users.department_id'] = $department_id;
        }
        if ( isset($_GET['gender']) && $_GET['gender'] != '' )
        {
            $gender = $_GET['gender'];
            $conditions['Users.gender'] = $gender;
        }

        
        $order = ['Users.modified Desc','Users.id Desc']; 
        if ( isset($_GET['sort']) && $_GET['sort'] != '' && isset($_GET['direction']) && $_GET['direction'] != '' )
        {
            array_unshift($order, 'Users.'.$_GET['sort'] . ' '. $_GET['direction']);
        } 


        if ($type == 'all') {
            $offset = $limit = null;
        }else{
            $limit = 20;
            $offset = isset($_GET['page'])&& intval($_GET['page'])>0 ? ($_GET['page']-1) *20 : 0;
        }
        
        $users = $this->Users->find('all',[
            'contain' => ['Roles','Departments','CountryCodes'],
            'limit' => $limit,
            'offset' => $offset,
            'conditions' => $conditions,
            'order' => $order
        ]);

        return $users;
    }


    /**
     * 获取部门下全部员工
     * @return [type] [description]
     */
    public function ajax(){
        $sWhere = $data = null;
        if(isset($_GET['department'])){
            $department = $this->request->query('department');
            if ($department) $sWhere['department_id = '] = $department;
            $data = $this->Users->find('all',[
                'conditions' => $sWhere ? $sWhere : [1],
                'fields' => ['id','username']
            ]);            
        }elseif(isset($_GET['user']) && $_GET['user'] !== '') {
            $user = $this->request->query('user');
            $data = $this->Users->get($user,[
                'fields' => ['department_id'],
                'conditions' => ['state' => 1]
            ]);        
        }

        $this->response->body(json_encode($data));
        return $this->response;
    }



    public function autocompelete(){
        $userArr = $data = [];
        
        $username = $this->request->query('query');
        $conditions = [
            'username LIKE ' => '%' . $username . '%'
        ];

        $selected_id = $this->request->query('selected_id');
        if ($selected_id) {
            $selected_id = array_filter(explode(',', $selected_id));
            !empty($selected_id) && $conditions['id not in'] = $selected_id;
        }
        
        $query = $this->Users->find('all',[
            'conditions' => $conditions,
            'fields' => ['Users.id','Users.username']
        ]);
        foreach ($query as $user) {
            $dataArr = [];
            $dataArr['value'] = $user->username;
            $dataArr['data'] = ['id' => $user->id];
            $userArr[] = $dataArr;
        }
        $data = [
            "query" => "Unit",
            "suggestions" => $userArr,
        ];
        $this->response->body(json_encode($data));
        return $this->response;
    }
    public function bulk()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        if(!empty($request['ids'])){
            switch ($request['submit']) {
                case 'del':
                    $this->Users->deleteAll(['id in' => $request['ids']]);
                    $this->Users->Customers->deleteAll(['user_id in' => $request['ids']]);
                    $this->Users->CustomerCommissions->deleteAll(['user_id in' => $request['ids']]);
                    $this->Users->Campaigns->deleteAll(['user_id in' => $request['ids']]);
                    $this->Users->Businesses->deleteAll(['user_id in' => $request['ids']]);
                    $this->Users->BusinessStatuses->deleteAll(['user_id in' => $request['ids']]);
                    $this->Flash->success(__('删除成功.'));
                    break;
                case 'export':
                    $conditions = ['Users.id in' => $request['ids']];

                    $users = $this->Users->find()
                        ->contain(['Departments','Roles','CountryCodes'])
                        ->where($conditions);

                    $this->export($users);
                    break;
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }

    public function export()
    {
        $users = $this->Users->find()
            ->contain(['Departments','Roles','CountryCodes']);

        $this->generate_excel($users);
    }

    private function generate_excel($users){
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
            ->setCellValue('C1', '部门')
            ->setCellValue('D1', '职位')
            ->setCellValue('E1', '性别')
            ->setCellValue('F1', '电话区号')
            ->setCellValue('G1', '电话')
            ->setCellValue('H1', '状态');

        $row = 2;
        $genderArr = ['男','女'];
        $stateArr = ['离职','在职'];
        foreach ($users as $user) {
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $user['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$row, $user['username']);
            if(isset($user['department']['name'])){
                $objPHPExcel->getActiveSheet()->setCellValue('C'.$row,$user['department']['name']);
            }
            if(isset($user['role']['name'])){
                $objPHPExcel->getActiveSheet()->setCellValue('D'.$row,$user['role']['name']);
            }
            if(isset($user['gender'])){
                $objPHPExcel->getActiveSheet()->setCellValue('E'.$row,$genderArr[$user['gender']]);
            }
            if(isset($user['country_code_id'])){
                $objPHPExcel->getActiveSheet()->setCellValue('F'.$row,$codes[$user['country_code_id']]);
            }
            if(isset($user['mobile'])){
                $objPHPExcel->getActiveSheet()->setCellValue('G'.$row,$user['mobile']);
            }
            
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$row,$stateArr[$user['state']]);
            
            $row++;
        }

        // 重命名工作sheet
        $objPHPExcel->getActiveSheet()->setTitle('sheet');

        // 设置第一个sheet为工作的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"export_users.xlsx\"");
        header('Cache-Control: max-age=0');
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    public function import()
    {   

        if ($this->request->isPost() || $this->request->isPut()) {
            $fileOK = $this->uploadFiles(array('xls','xlsx'),'files' . DS  . 'import', $this->request->data['File'], date('YmdH',time()));
            if (array_key_exists('urls', $fileOK)) {
                $this->loadModel('Departments');
                $this->loadModel('CountryCodes');
                set_time_limit(0);
                require_once(ROOT . DS  . 'vendor' . DS  . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel.php');
                require_once(ROOT . DS  . 'vendor' . DS  . 'PHPExcel' . DS . 'Classes' . DS . 'PHPExcel' . DS . 'IOFactory.php');

                $reader = \PHPExcel_IOFactory::load($fileOK['urls'][0]['full_url']);
                $objWorksheet = $reader->getActiveSheet()->toArray(null,true,true,true);
                
                if (count($objWorksheet)>2000) {
                    $this->Flash->error(__('导入不可超过2000行，请分批导入'));
                    return $this->redirect(['action' => 'import']);
                }
                $codes = $this->CountryCodes->find('all')
                    ->combine('id', 'country_code')
                    ->toArray();
                $codes = array_filter($codes);
                $departmentsArr = $this->Departments->find('list')->toArray();
                $usersArr = $this->Users->find('list')->toArray();
                $roleArr = $this->loadModel('Roles')->find('list')->toArray();

                $countRow = 1;
                $datas = $data = [];
                $stateArr = ['離職','在職'];
                $genderArr = ['男','女'];

                $query_user = $this->Users->query()->insert(['username','password','state','created','modified','department_id','role_id','country_code_id','mobile','gender']);
                $query_dep = $this->Departments->query()->insert(['name','created','modified']);
                $newMobilesArr = $newUsersArr = $newDpartmentsArr = [];
                
                //插入新员工以及新开发商
                $now = date('Y-m-d h:i:s');
                $password = (new DefaultPasswordHasher)->hash('123456');

                foreach ($objWorksheet as $row) {//检测表格中的错误
                    if($countRow > 1){
                        if (!$row['A'] && !$row['B'] && !$row['C'] && !$row['D'] && !$row['E'] && !$row['F'] && !$row['G']) break;
                        $data = $row;
                        $data['row'] = $countRow;
                        if ($row['A'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行员工姓名(A' . $countRow . ')為空'));
                            return $this->redirect(['action' => 'import']);
                        }
                        
                        if ($row['B'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行部门(I' . $countRow . ')為空'));
                            return $this->redirect(['action' => 'import']);
                        }else{                            
                            //檢測部门是否存在
                                 
                            $department_id = array_search($row['B'], $departmentsArr);
                            if (!$department_id && !isset($newDevelopersArr[$row['B']])) {//部门在系统在不存在
                                $newDepartmentsArr[$row['B']] = 1;
                                 
                            } elseif($department_id){
                                $data['department_id'] = $department_id;
                            }

                        }
                        if ($row['C'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行职位(C' . $countRow . ')為空'));
                            return $this->redirect(['action' => 'import']);
                        }else{
                            $role_id = array_search($row['C'], $roleArr);
                            if ($role_id === false) {
                                $this->Flash->error(__('excel中第' . $countRow . '行职位(C' . $countRow . ')无效'));
                                return $this->redirect(['action' => 'import']);
                            }else{
                                $data['role_id'] = $role_id;
                            }
                        }

                        if ($row['D'] == '') {
                            $data['gender'] = 0;
                        }else{
                            $gender = array_search($row['D'], $genderArr);
                            $data['gender'] = $gender===false ? 0 :$gender;
                            
                        }

                        if ($row['F'] != '') {
                            $country_code_id = $row['E'] == '852' ? 1 : array_search($row['E'], $codes);
                            if ($country_code_id === false) {
                                $this->Flash->error(__('excel中第' . $countRow . '行區號(E' . $countRow . ')在客戶地區中不存在或格式錯誤'));
                                return $this->redirect(['action' => 'import']);
                            } else{
                                $data['E'] = $country_code_id;
                                $data['F'] = str_replace(' ', '', $row['F']);
                                if(isset($newMobilesArr[$data['F'].'|'.$data['E']])){
                                    $this->Flash->error(__('excel中第' . $countRow . '行电话號碼(F' . $countRow . ')重复'));
                                    return $this->redirect(['action' => 'import']);
                                }
                            }                           
                        }

                        if ($row['G'] == '') {
                            $data['state'] = 1;
                        }else{
                            $state = array_search($row['G'], $stateArr); 
                            $data['state'] = $state===false ? 1 :$state;                            
                        }

                        $data && $datas[] = $data;
                    }
                    if ($countRow == 2001) break;
                    $countRow++;
                }

                if (!empty($newDepartmentsArr)) {
                    foreach ($newDepartmentsArr as $key => $value) {
                        $query_dep = $query_dep->values(['name' => $key,'created' => $now, 'modified' => $now]);
                    }
                    
                    $query_dep->execute();
                    $departmentsArr = $this->Departments->find('list')
                        ->order('id DESC')
                        ->toArray();
                }
                //检测客户是否为新客户
                foreach ($datas as $key => &$data) {
                    if ($data['B'] && !isset($data['departmet_id'])) {
                        $data['departmet_id'] = array_search($data['B'], $departmentsArr); 
                    }

                    //檢測業務員是否存在
                    $user_id = array_search($data['A'], $usersArr);
                    if (!$user_id && !isset($newUsersArr[$data['A']])) {                                                 
                        $query_user = $query_user->values(['username' => $data['A'], 'password' => $password, 'state' => $data['state'],'created' => $now, 'modified' => $now,'department_id' => $data['departmet_id'],'role_id' => $data['role_id'],'mobile' => $data['F'],'country_code_id' => $data['E']]);
                        $newUsersArr[] = $data['A'];
                    } elseif($user_id){
                        $user = $this->Users->get($user_id);
                        $user = $this->Users->patchEntity($user,['modified' => $now,'department_id' => $data['departmet_id'],'role_id' => $data['role_id'], 'state' => $data['state'],'mobile' => $data['F'],'country_code_id' => $data['E']]);
                        $this->Users->save($user);
                    }
                }
                if(!empty($newUsersArr)) $query_user->execute();
                $this->updateUserLastOp($this->_user['id']);

                $this->Flash->success(__('成功导入资料.'));
                return $this->redirect(['action' => 'index']);
            } 
            $this->Flash->error(__('未能成功导入资料.'));
            $this->set(compact('customer'));
        }
    }
}
