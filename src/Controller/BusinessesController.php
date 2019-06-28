<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\I18n\Time;
use Cake\Filesystem\Folder;
use Cake\Auth\DefaultPasswordHasher;
/**
 * Businesses Controller
 *
 * @property \App\Model\Table\BusinessesTable $Businesses
 */
class BusinessesController extends AppController
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
                $businesses =$this->filter('all','export');
                $this->generate_excel($businesses);
            }
        }

        $stateArr = ['进行中','失败','成交'];

        $this->set(compact('stateArr'));
        $this->set($_GET);

        if (isset($_GET['user_id']) && $_GET['user_id']) {
            $username = $this->Businesses->Users->findById($_GET['user_id'])->first();
            $username && $username = $username->username;
            $this->set(compact('username'));
        }
    }

    /**
     * View method
     *
     * @param string|null $id Customer Event id.
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

        $authorized = $this->Businesses->findById($id)->contain(['Users'])->where($conditions)->count();
        if (!$authorized) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        } 

        $business = $this->Businesses->get($id, [
            'contain' => ['Events', 'Customers' => function($q){
                return $q->contain(['CustomerMobiles' =>function($query){
                    return $query->contain(['CountryCodes']);
                }]);
            }, 'Users', 'BusinessStatuses' => function($q)
            {
                return $q->order(['BusinessStatuses.modified DESC'])->contain(['Users','CustomerImages'])->limit(20);
            }]
        ]);

        $business->business_status_count = $this->Businesses->BusinessStatuses
            ->find()
            ->where(['business_id' => $id])
            ->count();

        $stateArr = ['进行中','失败','成交'];
        $this->set(compact('business','stateArr'));
        $this->set('_serialize', ['business']);
    }
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        
        $business = $this->Businesses->newEntity();
        if ($this->request->is(['patch', 'post', 'put'])) {  
            $data = $this->request->getData();

            if ($this->collisionDetect($data['customer_id'],$data['event_id'])) {
                $this->Flash->error(__('该订单已存在.请核实.'));
            }else{
                $business = $this->Businesses->patchEntity($business, $data);
                $business->event_type_id = $this->loadModel('Events')->get($data['event_id'])->event_type_id;
                $data['parted'] && $business->parted = $data['parted'].' 00:00:00';
                
                if ($this->Businesses->save($business)) {
                   
                    if ($data['status']) {
                        $business_status_table = $this->loadModel('BusinessStatuses');
                        $business_status = $business_status_table->newEntity([
                            'business_id' => $business->id,
                            'customer_id' => $data['customer_id'],
                            'status' => $data['status'],
                            'next_contact_time' => $data['next_contact_time'],
                            'user_id' =>  $this->_user['id'],
                            'next_note' => $data['next_note']
                        ]);
                        $business_status_table->save($business_status);
                    }
                    $images = $this->request->getData('images');
                    $images && $images = array_filter($images);
                    if (!(empty($images) || (count($images) ==1 && $images[0]['error'] == 4))) {
                    
                        if (!$this->saveImages($images,$business->customer_id,$business->id,$business_status->id)) {
                            $this->Flash->error(__('图片上传失败，请重试.'));
                        }
                    }
                    $this->updateCustomerModified(['id' => $data['customer_id']]);
                    $this->Flash->success(__('订单添加成功.'));
                    $this->updateUserLastOp($this->_user['id']);
                    return $this->redirect(['controller' => 'Customers','action' => 'view',$business->customer_id]);
                }
                $this->Flash->error(__('订单添加失败.请重试..'));
            }
            
        }
        $users = $this->Businesses->Users->find('list')->where(['state' => 1]);
        $departments = $this->Businesses->Users->Departments->find('list');

        $stateArr = ['进行中','失败','成交'];
        $this->set(compact('business','users','departments','stateArr','data','business_status'));
        $this->set('_serialize', ['business']);
    }


    /**
     * Edit method
     *
     * @param string|null $id Customer Event id.
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

        $authorized = $this->Businesses->findById($id)->contain(['Users'])->where($conditions)->count();
        if (!$authorized) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        }
        
        $business = $this->Businesses->get($id, [
            'contain' => ['Users','Events','Customers']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {

            $data = $this->request->getData();                          
                
            $business = $this->Businesses->patchEntity($business, $data);
            $data['parted'] && $business->parted = $data['parted'] .' 00:00:00';
            if ($this->Businesses->save($business)) {
                $this->Flash->success(__('订单修改成功.'));
                $this->updateUserLastOp($this->_user['id']);
                $this->updateCustomerModified(['id' => $business->customer_id]);
                
                return $this->redirect(['action' => 'view',$id]);
            }
            $this->Flash->error(__('订单修改失败.请重试.'));
        
        }
        $business->parted && $business->parted = (new Time($business->parted))->i18nFormat('yyyy-MM-dd');

        $stateArr = ['进行中','失败','成交'];
        $this->set(compact('business','stateArr'));
        $this->set('_serialize', ['business']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Customer Event id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $business = $this->Businesses->get($id);
        if ($this->Businesses->delete($business)) {
            $this->Businesses->BusinessStatuses->deleteAll(['business_id' => $id]);
            $folder = new Folder(WWW_ROOT.'files/customer-images/'.$business->customer_id.'/'.$id);
            $folder->delete();
            $this->Flash->success(__('订单删除成功.'));
        } else {
            $this->Flash->error(__('订单删除失败.请重试.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function ajaxList() 
    {

        $businesses = $this->filter();

        $this->set(compact('businesses'));
    }


    /**
     * 根据条件筛选
     * @return $customers
     */
    private function filter($type = '',$export = null) 
    {
        $this->_contain = $export? ['Customers' => function($q){
                return $q->contain(['Users','CustomerEmails','CustomerMobiles' => function($q){
                    return $q->contain(['CountryCodes']);
                },'Developers']);}] : ['Customers'];
        
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
        
        $order = ['Businesses.modified Desc','Businesses.id Desc']; 
        if ( isset($_GET['sort']) && $_GET['sort'] != '' && isset($_GET['direction']) && $_GET['direction'] != '' )
        {
            array_unshift($order, 'Businesses.'.$_GET['sort'] . ' '. $_GET['direction']);
        }  

        if ( isset($_GET['user_id']) && $_GET['user_id'] != '' )
        {   
            $user_id = $_GET['user_id'];
            $conditions['Businesses.user_id'] = $user_id;
        }

        if ( isset($_GET['customer']) && $_GET['customer'] != '' )
        {   
            $customer = $_GET['customer'];
            $conditions['Customers.name LIKE'] = "%".$customer."%";
        }
        if ( isset($_GET['username']) && $_GET['username'] != '' )
        {   
            $username = $_GET['username'];
            $conditions['Users.username LIKE'] = "%".$username."%";
        }
        if ( isset($_GET['event']) && $_GET['event'] != '' )
        {   
            $event = $_GET['event'];
            $conditions['Events.name LIKE'] = "%".$event."%";
        }
        if ( isset($_GET['state']) && !empty($_GET['state']) )
        {
            $state = $_GET['state'];
            $conditions['Businesses.state in'] = $state;
        }

        if ( isset($_GET['startTime']) && $_GET['startTime'] != '' )
        {
            $startTime = $_GET['startTime'];
            $conditions['Businesses.modified >='] = $startTime;
        }
        if ( isset($_GET['endTime']) && $_GET['endTime'] != '' )
        {
            $endTime = $_GET['endTime'];
            $conditions['Businesses.modified <='] = $endTime;
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

        $businesses = $this->Businesses->find('all',[
            'contain' => array_merge(['Users','Events','BusinessStatuses'],$this->_contain),
            'order' => $order,
            'limit' => $limit,
            'offset' => $offset,
            'conditions' => $conditions
        ]);
        return $businesses;
    }

    /**
     * 检测当前是否存在该订单
     * @param  [type] $customer_id [description]
     * @param  [type] $event_id    [description]
     * @return [type]              [description]
     */
    private function collisionDetect($customer_id,$event_id)
    {
        return $this->Businesses
            ->find()
            ->where(['customer_id' => $customer_id,'event_id' => $event_id])
            ->count();
    }

    public function bulk()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        if(!empty($request['ids'])){
            switch ($request['submit']) {
                case 'del':
                    $this->Businesses->deleteAll(['id in' => $request['ids']]);
                    $this->Businesses->BusinessStatuses->deleteAll(['business_id' => $id]);
                    $this->Flash->success(__('删除成功.'));
                    break;
                case 'signed':
                case 'closed':
                    $state = ['signed' => 2,'closed' => 1];
                    $this->Businesses
                        ->query()
                        ->update()
                        ->set(['state' => $state[$request['submit']]])
                        ->where(['id in' => $request['ids']])
                        ->execute();
                    $this->Flash->success(__('批量更新成功.'));
                    $customer_ids = $this->Businesses->find()
                        ->where(['id in' => $request['ids']])
                        ->select(['customer_id' => 'customer_id'])
                        ->extract('customer_id')
                        ->toArray();

                    $this->updateCustomerModified(['id in' => $customer_ids]);
                    $this->updateUserLastOp($this->_user['id']);
                    break;
                case 'status':
                    $now = (new Time())->i18nFormat('yyyy-MM-dd HH:mm:ss');
                    $query = $this->Businesses->BusinessStatuses->query()->insert(['customer_id','status','next_contact_time','next_note','user_id','created','modified','business_id','done']);
                    if ($request['status'] || $request['next_contact_time']) {

                        $customers_id = $this->Businesses->find()->where(['id in' => $request['ids']])->combine('id', 'customer_id')->toArray();
                        foreach ($customers_id as $business_id => $customer_id) {
                            $query->values(['customer_id' =>$customer_id,'status' =>$request['status'],'next_contact_time' =>$request['next_contact_time'],'next_note' =>$request['next_note'],'user_id' =>$this->_user['id'],'created' =>$now,'modified' =>$now,'business_id' =>$business_id,'done' =>0]);
                        }
                        $query->execute();
                        $this->updateBusinessModified(['id in' => $request['ids']]);
                        $this->updateCustomerModified(['id in' => $customers_id]);
                        $this->updateUserLastOp($this->_user['id']);
                        $this->Flash->success(__('批量更新成功.'));
                    }
                    break;
                case 'export':
                    $conditions = ['Businesses.id in' => $request['ids']];

                    $businesses = $this->Businesses->find('all',[
                        'contain' => ['Customers' => function($q){
                            return $q->contain(['Users','CustomerEmails','CustomerMobiles' => function($q){
                                return $q->contain(['CountryCodes']);
                            },'Developers']);
                        },'Events'],
                        'conditions' => $conditions
                    ]);
                    $this->generate_excel($businesses);
                    $this->Flash->success(__('导出成功.'));
                    break;
            }
        }
        
        return $this->redirect(['action' => 'index']);
    }


    public function export($event_id)
    {
        $conditions = $event_id ? ['event_id',$event_id]:[];
        $businesses = $this->Businesses->find('all',[
            'contain' => ['Customers' => function($q){
                return $q->contain(['Users','CustomerEmails','CustomerMobiles' => function($q){
                    return $q->contain(['CountryCodes']);
                },'Developers']);
            },'Events'],
            'conditions' => $conditions
        ]);

        $this->generate_excel($businesses);
    }

    private function generate_excel($businesses){
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
            ->setTitle("export_businesses")
            ->setSubject("")
            ->setDescription("")
            ->setKeywords("")
            ->setCategory("");


        //根据excel坐标，添加数据
        $objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', '订单编号')
            ->setCellValue('B1', '客户姓名')
            ->setCellValue('C1', '电话区号')
            ->setCellValue('D1', '电话(必填)')
            ->setCellValue('E1', '电话区号')
            ->setCellValue('F1', '电话')
            ->setCellValue('G1', '来源')
            ->setCellValue('H1', '电邮1')
            ->setCellValue('I1', '电邮2')
            ->setCellValue('J1', '业务员')
            ->setCellValue('K1', '开发商')
            ->setCellValue('L1', '活动名称')
            ->setCellValue('M1', '参与时间');

        $row = 2;

        foreach ($businesses as $business) {
            $customer = $business['customer'];
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$row, $business['id']);
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
            $objPHPExcel->getActiveSheet()->setCellValue('L'.$row,$business['event']['name']);

            if(isset($business['parted'])){
                $business['parted'] = (new Time($business['parted']))->i18nFormat('yyyy-MM-dd HH:mm:ss');
                $objPHPExcel->getActiveSheet()->setCellValue('M'.$row,$business['parted']);
            }
            $row++;
        }

        // 重命名工作sheet
        $objPHPExcel->getActiveSheet()->setTitle('sheet');

        // 设置第一个sheet为工作的sheet
        $objPHPExcel->setActiveSheetIndex(0);
        ob_end_clean(); 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"export_businesses.xlsx\"");
        header('Cache-Control: max-age=0');
        // 保存Excel 2007格式文件，保存路径为当前路径，名字为export.xlsx
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    public function import()
    {   

        if ($this->request->isPost() || $this->request->isPut()) {
            $event_id = $this->request->getData('event_id');
            $event_type_id = $this->loadModel('Events')->get($event_id)->event_type_id;
            $fileOK = $this->uploadFiles(array('xls','xlsx'),'files' . DS  . 'import', $this->request->data['File'], date('YmdH',time()));
            if (array_key_exists('urls', $fileOK)) {
                $this->loadModel('CustomerMobiles');
                $this->loadModel('CustomerEmails');
                $this->loadModel('BusinessStatuses');
                $this->loadModel('Businesses');
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
                $customersArr = $this->loadModel('Customers')->find('all')
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
                $query_business = $this->loadModel('Businesses')->query()->insert(['event_id','created','modified','customer_id','state','user_id','parted','event_type_id','remark']);
                $query_business_status = $this->loadModel('BusinessStatuses')->query()->insert(['status','created','modified','customer_id','next_contact_time','user_id','next_note','done','business_id']);
                $newUsersArr = $newDevelopersArr = $newCustomersArr = $newMobilesArr = $newEmailsArr = $oldCustomersArr =[];
                $businessStatusInsert = $businessInsert = $customerInsert = $mobileInsert = $emailInsert = 0;
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
                            return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                        }
                        
                        if ($row['I'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行業務員(I' . $countRow . ')為空'));
                            return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                        }
                        if (trim($row['G']) != '') {
                            $data['G'] = trim($row['G']);
                            if (!preg_match("/^[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[_\p{L}0-9][-_\p{L}0-9]*\.)*(?:[\p{L}0-9][-\p{L}0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/ui", $data['G'] )) {
                                    
                                $this->Flash->error(__('excel中第' . $countRow . '電郵1(G' . $countRow . ')格式不正確'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            }
                            if(isset($newEmailsArr[$row['G']])){
                                $this->Flash->error(__('excel中第' . $countRow . '行电邮1(G' . $countRow . ')重复'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            }else{
                                $newEmailsArr[$row['G']] = 1;
                            }
                        } 
                        if (trim($row['H']) != '') {
                            $data['H'] = trim($row['H']);
                            if (!preg_match("/^[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+)*@(?:[_\p{L}0-9][-_\p{L}0-9]*\.)*(?:[\p{L}0-9][-\p{L}0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})$/ui", $data['H'] )) {
                                    
                                $this->Flash->error(__('excel中第' . $countRow . '電郵2(H' . $countRow . ')格式不正確'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            }
                            if(isset($newEmailsArr[$row['H']])){
                                $this->Flash->error(__('excel中第' . $countRow . '行电邮2(H' . $countRow . ')重复'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            }else{
                                $newEmailsArr[$row['H']] = 1;
                            }
                        } 
                        if ($row['C'] == '') {
                            $this->Flash->error(__('excel中第' . $countRow . '行電話號碼1(C' . $countRow . ')為空'));
                            return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                        } else {
                            $country_code_id = $row['B'] == '852' ? 1 : array_search($row['B'], $codes);
                            if ($country_code_id === false) {
                                $this->Flash->error(__('excel中第' . $countRow . '行區號1(B' . $countRow . ')在客戶地區中不存在或格式錯誤'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            } else{
                                $data['B'] = $country_code_id;
                                $data['C'] = str_replace(' ', '', $row['C']);
                                if(isset($newMobilesArr[$data['C'].'|'.$data['B']])){
                                    $this->Flash->error(__('excel中第' . $countRow . '行电话號碼1(C' . $countRow . ')重复'));
                                    return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                                }else{
                                    $newMobilesArr[$data['C'].'|'.$data['B']] = 1;
                                }
                            }                           
                        }

                        if ($row['E'] != '') {
                            $country_code_id = $row['D'] == '852' ? 1 : array_search($row['D'], $codes);
                            if ($country_code_id === false) {
                                $this->Flash->error(__('excel中第' . $countRow . '行區號2(D' . $countRow . ')在客戶地區中不存在或格式錯誤'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            } else{
                                $data['D'] = $country_code_id;
                                $data['E'] = str_replace(' ', '', $row['E']);
                                if(isset($newMobilesArr[$data['E'].'|'.$data['D']])){
                                    $this->Flash->error(__('excel中第' . $countRow . '行电话號碼2(E' . $countRow . ')重复'));
                                    return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
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

                        
                        if($row['K']){ 
                            if (!preg_match("/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9]))?$/", $row['K'])) {
                                $this->Flash->error(__('excel中第' . $countRow . '参与时间(K' . $countRow . ')格式不正確'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            }
                            $data['parted'] = date('Y-m-d H:i:s',strtotime($row['K']));
                            
                        }else{
                            $data['parted']=null;
                        }
                        
                        if($row['M']){ 
                            if (!preg_match("/^\d{4}[\-](0?[1-9]|1[012])[\-](0?[1-9]|[12][0-9]|3[01])(\s+(0?[0-9]|1[0-9]|2[0-3])\:(0?[0-9]|[1-5][0-9]))?$/", $row['M'])) {
                                $this->Flash->error(__('excel中第' . $countRow . '参与时间(K' . $countRow . ')格式不正確'));
                                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
                            }
                            $data['next_contact_time'] = date('Y-m-d H:i:s',strtotime($row['M']));
                            
                        }else{
                            $data['next_contact_time']=null;
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
                    $exisit_business = null;
                    $data['new'] = $data['new_business'] = true;
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
                        
                        $customerInsert++;
                        $query_customer = $query_customer->values(['name' => $data['A'], 'user_id' => $data['user_id'],'developer_id' => $data['developer_id'],'source' => $data['F'],'created' => $now, 'modified' => $now,'remark' => 'import']); 
                    }else{//本行的电话或者电邮不止一个存在于系统中
                        $exist_c_id_arr = array_unique($exist_c_id_arr);
                        if (count($exist_c_id_arr) !=1) {//若存在多个customer_id
                            $this->Flash->error(__('excel中第' . $data['row'] . '行中的'.implode(',', array_keys($exist_c_id_arr)).'存在于系统中，但为属于不同客户，请核实'));
                            return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
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

                            //若订单已经存在。更新订单
                            $exisit_business = $this->Businesses->find()->where(['customer_id' => $data['customer_id'],'event_id' =>$event_id])->first();
                            
                            if (count($exisit_business)) {
                                if($data['parted']){
                                    $exisit_business->parted = $data['parted'];
                                    $this->Businesses->save($exisit_business);
                                }  
                                $data['new_business'] = false;
                                $data['business_id'] = $exisit_business->id;
                            }else{
                                $data['new_business'] = true;
                            }
                            $data['new'] = false;
                            $data['user_id'] = $query->user_id; 
                                                      
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
                    if ($data['new_business']) {
                        $businessInsert++;
                        $query_business->values(['event_id' => $event_id,'created'=> $now,'modified'=> $now,'customer_id' =>$data['customer_id'],'state'=> 0,'user_id'=> $data['user_id'],'parted' => $data['K'],'event_type_id' => $event_type_id,'remark' => 'import']);
                    }
                    
                }

                if($mobileInsert) $query_mobile->execute();
                if($emailInsert) $query_email->execute();
                if($businessInsert) {
                    $query_business->execute();
                    $businessIdArr = $this->Businesses->find()->where(['remark' => 'import'])->order(['id DESC'])->limit($businessInsert)->extract('id')->toArray();
                }

                //
                foreach ($datas as &$data) {
                    if ($data['new_business']) $data['business_id'] = array_pop($businessIdArr);
                    if ($data['L'] || $data['next_contact_time'] || $data['N']) {
                        $query_business_status->values(['status' => $data['L'],'created'=> $now,'modified'=> $now,'customer_id' =>$data['customer_id'],'next_contact_time'=> $data['next_contact_time'],'user_id'=> $data['user_id'],'next_note' => $data['N'],'business_id' => $data['business_id']]);
                        $businessStatusInsert = 1;
                    }
                    
                }
                $businessStatusInsert && $query_business_status->execute();

                $this->updateUserLastOp($this->_user['id']);
                $this->Flash->success(__('成功导入资料.'));
                return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
            } 
            $this->Flash->error(__('未能成功导入资料.'));
            return $this->redirect(['controller' => 'Events','action' => 'view',$event_id]);
        }        
    }



    private function transferSql($to_user_id,$conditions){
        $this->loadModel('Customers');
        $this->loadModel('Businesses');
        $this->loadModel('BusinessStatuses');

        $customers_id = $this->Customers->find()->where($conditions)->extract('id')->toArray();
        if (!empty($customers_id)) {
            $this->Customers->query()
                ->update()
                ->set(['user_id' => $to_user_id])
                ->contain(['Businesses'])
                ->where($conditions)
                ->execute();
            $this->Businesses->query()
                ->update()
                ->set(['user_id' => $to_user_id])
                ->where(['customer_id in' => $customers_id])
                ->execute();
            $this->BusinessStatuses->query()
                ->update()
                ->set(['user_id' => $to_user_id])
                ->where(['customer_id in' => $customers_id])
                ->execute();
        }
    }
}
