<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\I18n\Time;
use Cake\I18n\Date;
use Cake\Event\Event;
use Cake\Utility\Inflector;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{
    public $action_hows = [
        'index' => 'v',
        'campaign' => 'v',
        'search' => 'v',
        'view' => 'v',
        'edit' => 'e',
        'add' => 'a',
        'addImages' => 'a',
        'ajaxList' => 'v',
        'ajax' => 'v',
        'ajaxUsers' => 'v',
        'addImages' => 'a',
        'delete' => 'd',
        'deleteMobile' => 'e',
        'deleteEmail' => 'e',
        'import' => 'i',
        'export' => 'o',
        'login' => 'v',
        'logout' => 'v',
        'addSms' => 'a',
        'addEmail' => 'a',
        'addMms' => 'a',
        'event' => 'a',
        'autocompelete' => 'v',
        'getEvent' => 'v',
        'transfer' => 't',
        'transferEntire' => 't',
        'transferFilter' => 't',
        'bulk' => 'v',
        'done' => 'v',
        'draw' => 'v',
        'sendReporter' => 'a',
        'resend' => 'a',
        'sync' => 'a',
    ];
    public $controller_modules = [
        'Statistics' => 'Statistics',
        'Privileges' => 'Privileges',
        'Configs' => 'Configs',
        'Customers' => 'Customers',
        'CustomerImages' => 'Customers',
        'Businesses' => 'Businesses',
        'BusinessStatuses' => 'Businesses',
        'Events' => 'Events',
        'EventTypes' => 'Events',
        'Campaigns' => 'Campaigns',
        'Users' => 'Users',
        'Departments' => 'Departments',
        'Developers' => 'Configs',
        'CountryCodes' => 'Configs',
        'CustomerCommissions' => 'Customers',
        'Crons' => 'Customers',
        'AuthNodes' => 'AuthNodes',
    ];

    public $_modules;
    public $_privileges;
    public $_user;
    public $_action;
    public $_controller;

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * e.g. `$this->loadComponent('Security');`
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();

        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'userid']
                ]
            ],
            'loginRedirect' => [
                'controller' => 'Customers',
                'action' => 'index'
            ],
            'unauthorizedRedirect' => $this->referer()
        ]);

        /*
         * Enable the following components for recommended CakePHP security settings.
         * see http://book.cakephp.org/3.0/en/controllers/components/security.html
         */
        // $this->loadComponent('Security');
        // $this->loadComponent('Csrf');
    }

    public function beforeFilter(Event $event)
    {   
        $this->Auth->allow(['draw','sendReporter','login']);
        
        $session = $this->request->session();
        if (isset($session->read('Auth')['User'])){
            $this->_user = $session->read('Auth')['User'];
            $session = $this->request->session();
            $role_id = $session->read('Auth')['User']['role_id'];
            $auth_nodes = $this->getAuthNodesByRoleId($role_id);
            $privileges = $this->checkAuth($auth_nodes);
            $navs = $this->getNavigator($auth_nodes);
            $this->_conditions = $this->calDataField($this->_user);
            $this->set('_privileges',$privileges);
            $this->set('_user',$this->_user);
            // //读取今日应联系的客户名单
            // $businessStatusesTable = $this->loadModel('BusinessStatuses')->find();
            // $ahead = $this->loadModel('Configs')->findByName('ahead')->first()->value?:1;

            // $data = $this->request->getQuery();
            // if(isset($data['business_status_id']) && isset($data['done'])){
            //     $this->loadModel('BusinessStatuses')->query()->update()->set(['done' => 1])->where(['id' => $data['business_status_id']])->execute();
            // }

            // $todolist = $businessStatusesTable
            //     ->where([
            //         'done' => 0,
            //         'BusinessStatuses.user_id' => $this->_user['id'],
            //         'next_contact_time <' => (new Date("+$ahead day"))->i18nFormat('yyyy-MM-dd HH:mm:ss')
            //     ])
            //     ->contain(['Customers' => function($q){
            //         return $q->contain(['CustomerMobiles' => function($q){
            //             return $q->contain(['CountryCodes']);
            //         }]);
            //     }])
            //     ->select([
            //         'id' => 'BusinessStatuses.id',
            //         'name' => 'Customers.name',
            //         'next_contact_time' => 'BusinessStatuses.next_contact_time',
            //         'next_note' => 'BusinessStatuses.next_note',
            //         'status' => 'BusinessStatuses.status'
            //     ])
            //     ->enableAutoFields(true)
            //     ->map(function($row)
            //     {
            //         $row->next_contact_time = (new Time($row->next_contact_time))->i18nFormat('MM-dd HH:mm:ss');
            //         $mobile = $row->customer->customer_mobiles[0];
            //         $row->mobile = '+' .$mobile->country_code->country_code . '-'.$mobile->mobile;
            //         return $row;
            //     })
            //     ->toArray();

            // $stateArr = array_filter(explode('|', $this->loadModel('Configs')->findByName('state')->first()->value));
            // $sourceArr = array_filter(explode('|', $this->loadModel('Configs')->findByName('source')->first()->value));
            // $stateColorArr = ['','negative','positive','','','','','',''];
            // $this->stateArr = $stateArr;
            // $this->sourceArr = $sourceArr;
            $this->set(compact('stateArr','stateColorArr','todolist','warning','sourceArr','navs'));

        } 

    }
    // 检查功能权限
    protected function checkAuth($auth_nodes)
    {
        $session = $this->request->session();
        $auth_routes = array_column($auth_nodes, 'routing_address');
        $current_route_address = Inflector::dasherize($this->request->controller).'/'.Inflector::dasherize($this->request->action);
        if(!in_array($current_route_address, $auth_routes)) {
            if ($this->request->is('ajax')) {
                $this->response->body('authorized_wrong');
                return $this->response;
            } else {
                $this->Flash->error(__('无权访问该页面.'));
                $refer = $this->referer();
                if (strpos($refer, 'users/login')) {
                    $refer='/';
                }
                #return $this->redirect($this->referer());
            }
        }
        return $auth_routes;
    }
    //获取角色拥有的权限
    protected function getAuthNodesByRoleId($role_id){
        $auth_node_ids = $this->loadModel('Roles')->findById($role_id)->first()->auth_node_ids;
        $auth_node_ids = explode(',', $auth_node_ids);
        $auth_nodes = [];
        if(!empty($auth_node_ids)){
            $auth_nodes = $this->loadModel('AuthNodes')->find('all',[
                'conditions' => ['id in' => $auth_node_ids],
            ])  
                ->hydrate(false)
                ->toArray();
        }
        return $auth_nodes;
    }
    // 检查角色数据权限
    protected function calDataField($user)
    {
        switch ($user['role_id']) {
            case 1:
                $conditions = ['Users.id' => $user['id']];
                break;
            case 2:
                $user_ids = $this->loadModel('UserDepartments')->find('all')
                    ->where(['department_id in' => $user['department_ids']])
                    ->extract('user_id')
                    ->toArray();
                $conditions = ['Users.id in' => $user_ids,'Users.role_id <= ' => $user['role_id']];
                break;            
            case 3:
            case 4:
                $conditions = [];
                break;
        }
        return $conditions;
    }
    //获取导航栏
    protected function getNavigator($auth_nodes){
        $navigators = $this->formatNavigator($auth_nodes,0);
        return $navigators;
    }
    // 格式化导航栏
    protected function formatNavigator($arr,$pid){
        $res = [];
        foreach ($arr as $k => $v) {
            if($v['is_nav'] && $v['parent_id'] == $pid){
                $res[] = $v;
                unset($arr[$k]);
                $tmp = $this->formatNavigator($arr,$v['id']);
                if(!empty($tmp)){
                    foreach ($res as $kk => &$vv) {
                        if($vv['id'] == $tmp[0]['parent_id']){
                            $vv['sub'] = $tmp;
                        }
                    }
                }
                continue;
            }
        }
        return $res;
    }

    /**
     * Before render callback.
     *
     * @param \Cake\Event\Event $event The beforeRender event.
     * @return \Cake\Network\Response|null|void
     */
    public function beforeRender(Event $event)
    {
        if (!array_key_exists('_serialize', $this->viewVars) &&
            in_array($this->response->type(), ['application/json', 'application/xml'])
        ) {
            $this->set('_serialize', true);
        }
    }

    protected function uploadFiles($accepts,$folder, $formdata, $itemId = null) {
        // setup dir names absolute and relative
        $folder_url = WWW_ROOT . $folder;
        $rel_url = $folder;

        // create the folder if it does not exist
        if (!is_dir($folder_url)) {
            mkdir($folder_url,0777,true);
        }

        // if itemId is set create an item folder
        if ($itemId) {
            // set new absolute folder
            $folder_url = WWW_ROOT . $folder . '/' . $itemId;
            // set new relative folder
            $rel_url = $folder . '/' . $itemId;
            // create directory
            if (!is_dir($folder_url)) {
                mkdir($folder_url);
            }
        }

        // list of permitted file types, this is only images but documents can be added
        $permitted = $accepts;

        // loop through and deal with the files
        foreach ($formdata as $file) {
            $filename = str_replace(' ', '_', $file['name']);
            $fileInfo = explode(".", $filename);
            $fileExtension = end($fileInfo);
            $name = substr($filename , 0 , strlen($filename)-strlen($fileExtension)-1);
            $typeOK = false;
            // check filetype is ok
            // debug ($file['type']);
            foreach ($permitted as $type) {
                if ($type == $fileExtension) {
                    $typeOK = true;
                    break;
                }
            }

            // if file type ok upload the file
            if ($typeOK) {
                // switch based on error code
                $filename = iconv('utf-8','gbk',$filename);
                switch ($file['error']) {
                    case 0:
                        // check filename already exists
                        if (!file_exists($folder_url . '/' . $filename)) {
                            // create full filename
                            $full_url = $folder_url . '/' . $filename;
                            $url = $rel_url . '/' . $filename;
                            // upload the file
                            
                            $success = move_uploaded_file($file['tmp_name'], $full_url);
                        } else {
                            // create unique filename and upload file
                            ini_set('date.timezone', 'Europe/London');
                            $name = $name.'_'.date('Y_m_d_H_i_s');
                            $full_url = $folder_url . '/' . $name .'.'. $fileExtension;
                            $full_url = iconv('utf-8','gb2312',$full_url);
                            $url = $rel_url . '/' . $name .'.'. $fileExtension;
                            $success = move_uploaded_file($file['tmp_name'], $full_url);
                        }
                        // if upload was successful
                        if ($success) {
                            // save the url of the file
                            $result['urls'][] = ['full_url' => $full_url,'path'=> $folder_url,'rel_path'=> $rel_url,'filename'=> $name,'ext'=> $fileExtension,'url' =>$url];
                        } else {
                            $result['errors'][] = "Error uploaded $filename. Please try again.";
                        }
                        break;
                    case 3:
                        // an error occured
                        $result['errors'][] = "Error uploading $filename. Please try again.";
                        break;
                    default:
                        // an error occured
                        $result['errors'][] = "System error uploading $filename. Contact webmaster.";
                        break;
                }
            } elseif ($file['error'] == 4) {
                // no file was selected for upload
                $result['nofiles'][] = "No file Selected";
            } else {
                // unacceptable file type
                $result['errors'][] = "$filename. Unacceptable file types " ;
            }
        }
        return $result;
    }

    protected function uploadFile($accepts,$folder, $file, $itemId = null) {
        // setup dir names absolute and relative
        $folder_url = WWW_ROOT . $folder;
        $rel_url = $folder;

        // create the folder if it does not exist
        if (!is_dir($folder_url)) {
            mkdir($folder_url);
        }

        // if itemId is set create an item folder
        if ($itemId) {
            // set new absolute folder
            $folder_url = WWW_ROOT . $folder . '/' . $itemId;
            // set new relative folder
            $rel_url = $folder . '/' . $itemId;
            // create directory
            if (!is_dir($folder_url)) {
                mkdir($folder_url);
            }
        }

        // list of permitted file types, this is only images but documents can be added
        $permitted = $accepts;

        // loop through and deal with the files
        
        $filename = str_replace(' ', '_', $file['name']);
        $fileInfo = explode(".", $filename);
        $fileExtension = end($fileInfo);
        $name = substr($filename , 0 , strlen($filename)-strlen($fileExtension)-1);
        $typeOK = false;
        // check filetype is ok
        // debug ($file['type']);
        foreach ($permitted as $type) {
            if ($type == $fileExtension) {
                $typeOK = true;
                break;
            }
        }

        // if file type ok upload the file
        if ($typeOK) {
            // switch based on error code
            $filename = iconv('utf-8','gbk',$filename);
            switch ($file['error']) {
                case 0:
                    // check filename already exists
                    if (!file_exists($folder_url . '/' . $filename)) {
                        // create full filename
                        $full_url = $folder_url . '/' . $filename;
                        $url = $rel_url . '/' . $filename;
                        // upload the file
                        
                        $success = move_uploaded_file($file['tmp_name'], $full_url);
                    } else {
                        // create unique filename and upload file
                        ini_set('date.timezone', 'Europe/London');
                        $name = $name.'_'.date('y_m_d_H_i_s');
                        $full_url = $folder_url . '/' . $name .'.'. $fileExtension;
                        $full_url = iconv('utf-8','gb2312',$full_url);
                        $url = $rel_url . '/' . $name .'.'. $fileExtension;
                        $success = move_uploaded_file($file['tmp_name'], $full_url);
                    }
                    // if upload was successful
                    if ($success) {
                        // save the url of the file
                        $result['url'] = ['full_url' => $full_url,'path'=> $folder_url,'rel_path'=> $rel_url,'filename'=> $name,'ext'=> $fileExtension,'url' =>$url];
                    } else {
                        $result['error'] = "Error uploaded $filename. Please try again.";
                    }
                    break;
                case 3:
                    // an error occured
                    $result['error'] = "Error uploading $filename. Please try again.";
                    break;
                default:
                    // an error occured
                    $result['error'] = "System error uploading $filename. Contact webmaster.";
                    break;
            }
        } elseif ($file['error'] == 4) {
            // no file was selected for upload
            $result['error'] = "No file Selected";
        } else {
            // unacceptable file type
            $result['error'] = "$filename. Unacceptable file types " ;
        }
    
        return $result;
    }

    protected function updateUserLastOp($id){
        $user = $this->loadModel('Users')->get($id);
        $user->last_op = Time::now();
        $this->Users->save($user);
    }
    protected function updateBusinessModified($conditions){        
        $this->loadModel('Businesses')->query()
            ->update()
            ->set(['modified' => date('Y-m-d H:i:s',time())])
            ->where($conditions)
            ->execute();

    }
    protected function updateCustomerModified($conditions){
        $this->loadModel('Customers')->query()
            ->update()
            ->set(['modified' => date('Y-m-d H:i:s',time())])
            ->where($conditions)
            ->execute();
    }

    protected function saveImages($files,$customer_id,$business_id = 0,$business_status_id=0)
    {
        $rel_path = 'files' . DS  . 'customer-images'. DS .$customer_id.($business_id ? DS.$business_id.DS.$business_status_id : '');
        $fileOK = $this->uploadFiles(['png','gif','jpeg','jpg'],$rel_path, $files);
        if (array_key_exists('urls', $fileOK)) {            
            $now = Time::now();            
            $images = $this->loadModel('CustomerImages')->query()->insert(['path','customer_id','created','name','ext','business_id','business_status_id']);
            foreach ($fileOK['urls'] as $url) {
                $images->values(['path' =>  '/'.$rel_path.'/','customer_id'=>$customer_id,'business_id'=>$business_id,'business_status_id'=>$business_status_id,'created' => $now,'name'=> $url['filename'],'ext'=> $url['ext']]);
            }
            $images->execute();
            return $fileOK;
        }else{
            return false;
        }
    }
}
