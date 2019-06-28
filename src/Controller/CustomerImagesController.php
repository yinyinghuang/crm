<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Event\Event;
/**
 * CustomerImages Controller
 *
 * @property \App\Model\Table\CustomerImagesTable $CustomerImages
 */
class CustomerImagesController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);

        intval($this->request->getData('customer_id')) && $customer_id = intval($this->request->getData('customer_id'));
        intval($this->request->query('customer_id')) && $customer_id = intval($this->request->query('customer_id'));

        if (!isset($customer_id) || !$customer_id) { 
            if ($this->request->is('ajax')) {
                $this->response->body('parameter_error');
                return $this->response;
            } else {
                $this->Flash->error('系统错误');
                return $this->redirect($this->referer());
            }
        }

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

        $customer = $this->loadModel('Customers')
            ->findById($customer_id)
            ->contain(['Businesses','Users'])
            ->where($conditions)
            ->first();
        if (!$customer) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        } 
        $this->_customer_id = $customer_id;
        $this->set(compact('customer'));

    }

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $eventTypes = $this->loadModel('EventTypes')->find('list');
        $this->set($_GET);
        if (isset($_GET['event_id'])) {
            $event_id = array_filter(explode(',',$_GET['event_id']));
            !empty($event_id) && $event_names = $this->loadModel('Events')->find('list')->where(['id in' => $event_id]);
        }
        $this->set(compact('eventTypes','customers','user_name','event_names'));

    }


    public function ajaxList()
    {
        $customerImages = $this->filter('',$this->_customer_id);


        $business_ids = array_filter(array_column($customerImages->hydrate(false)->toArray(), 'business_id'));
        
        !empty($business_ids) && $businesses = $this->loadModel('Businesses')->find()
            ->select(['id' => 'Businesses.id','name' => 'Events.name'])
            ->where(['Businesses.id in' => $business_ids])
            ->contain(['Events'])
            ->combine('id','name')
            ->toArray();

        $this->set(compact('customerImages','businesses'));
    }

    /**
     * 根据条件筛选
     * @return $customers
     */
    private function filter($type = '',$customer_id) 
    {
        $conditions = null;
        if ( isset($_GET['name']) && $_GET['name'] != '' )
        {   
            $name = $_GET['name'];
            $conditions['CustomerImages.name LIKE'] = "%".$name."%";
        }
        if ( isset($_GET['non_involved_business']) && $_GET['non_involved_business'] != '' )
        {   
            $non_involved_business = $_GET['non_involved_business'];
            $conditions['CustomerImages.business_id'] = 0;
        }
        if ( isset($_GET['startTime']) && $_GET['startTime'] != '' )
        {
            $startTime = $_GET['startTime'];
            $conditions['CustomerImages.created >='] = $startTime;
        }
        if ( isset($_GET['endTime']) && $_GET['endTime'] != '' )
        {
            $endTime = $_GET['endTime'];
            $conditions['CustomerImages.created <='] = $endTime;
        } 
        if ( isset($_GET['event_id']) && $_GET['event_id'] != ',' )
        {

            $event_id = array_filter(explode(',',$_GET['event_id']));
            !empty($event_id) && $conditions['Businesses.event_id in'] = $event_id;            

        }

        if ( isset($_GET['state']) && !empty($_GET['state']))
        {
            $conditions['Businesses.state in'] = $_GET['state'];
        }
        $order = ['CustomerImages.created DESC','Businesses.modified DESC'];
        if ($type == 'all') {
            $offset = $limit = null;
        }else{
            $limit = 20;
            $offset = isset($_GET['page'])&& intval($_GET['page'])>0 ? ($_GET['page']-1) *20 : 0;
        }

        $customerImages = $this->CustomerImages->find()
            ->select(['id' => 'CustomerImages.id','created' => 'CustomerImages.created','path' => 'CustomerImages.path','name' => 'CustomerImages.name','ext' => 'CustomerImages.ext','business_id' => 'Businesses.id',])
            ->contain(['Customers'])
            ->leftJoin('Businesses','Businesses.id=CustomerImages.business_id')
            ->where($conditions)
            ->order($order)
            ->limit($limit)
            ->offset($offset);
        

        return $customerImages;
    }

    public function bulk()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        if(!empty($request['ids'])){
            switch ($request['submit']) {
                case 'del':
                    $images = $this->CustomerImages->find()
                        ->where(['id in' => $request['ids'],'customer_id' => $this->_customer_id])
                        ->toArray();
                    
                    if (!empty($images)) {
                        
                        if ($this->deleteDatabaseAndFileBatch($images)) {
                            $this->Flash->success(__('图片删除成功.'));
                        }else{
                            $this->Flash->error(__('部分图片删除失败，请重试'));
                        }
                    } else{
                        $this->Flash->error(__('图片不存在.'));
                    } 

                    break;
            }
        }
        
        return $this->redirect(['action' => 'index','?' => ['customer_id' => $this->_customer_id]]);
    }


    public function delete(){
        $this->request->allowMethod(['post', 'delete']);
        $image_id = $this->request->data['id'];
        $customer_id = $this->request->data['customer_id'];
        $res['success'] = true;
        $image = $this->CustomerImages->find()
            ->where(['id' => $image_id,'customer_id' => $customer_id])
            ->first();
        if ($image) {
            if (!$this->deleteDatabaseAndFile($image)) {
                $res['success'] = false;
                $res['error'] = '图片删除失败，请重试';
            }
        }   
        $this->response->body(json_encode($res));
        return $this->response;
    }

    private function deleteDatabaseAndFile($image){
        $filepath = [];
        $res = true;
        $filepath['image'] = WWW_ROOT .$image->path.$image->name.'.'.$image->ext;
        $filepath['thumb'] = WWW_ROOT .$image->path.$image->name.'.thumb.'.$image->ext;
        foreach ($filepath as $path) {
            $path = iconv('utf-8','gb2312',$path);
            if (file_exists($path)) {                
                $file = new File($path);
                $res = $res && $r=$file->delete();
            }
        }
        $this->CustomerImages->delete($image);        
        return $res;
    }

    /**
     * 批量删除图片
     */
    protected function deleteBatch(){
        
        $image_ids = $this->request->data['ids'];
        $customer_id = $this->request->data['customer_id'];
        if (empty($image_ids) || !$customer_id) {
            $this->Flash->error(__('参数错误，请重试'));
            return $this->redirect($this->referer());
        }

        $images = $this->CustomerImages->find()
            ->where(['id in' => $image_ids,'customer_id' => $customer_id])                                                 
            ->toArray();
        //     $this->response->body($images);
        // return $this->response();
        if (!empty($images)) {
            
            if ($this->deleteDatabaseAndFileBatch($images)) {
                $this->Flash->success(__('图片删除成功.'));
            }else{
                $this->Flash->error(__('部分图片删除失败，请重试'));
            }
        } else{
            $this->Flash->error(__('图片不存在.'));
        } 
        return $this->redirect(['action' => 'view',$customer_id]);
    }


    private function deleteDatabaseAndFileBatch($images){

        $ids = $error_ids = [];
        $res = true;
        foreach ($images as $image) {
            $filepath = [];
            $filepath['image'] = WWW_ROOT .$image->path.$image->name.'.'.$image->ext;
            $filepath['thumb'] = WWW_ROOT .$image->path.$image->name.'.thumb.'.$image->ext;
            foreach ($filepath as $path) {

                if(file_exists($path)){//文件存在
                    $file = new File($path);

                    if ($delete_res = $file->delete()) {//删除文件成功
                        $ids[] =  $image->id;//id存入ids中
                    }
                    $res = $res && $delete_res;
                }else{//文件不存在，直接存入ids
                   $ids[] =  $image->id;
                }
            }
        }      
        $this->CustomerImages->deleteAll(['id in' => $ids]);
        return $res;
    }

    public function search() 
    {

        switch ($this->_user['role_id']) {
            case 1:
                $sWhere['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $sWhere['Users.role_id <= '] = $this->_user['role_id'];
                $sWhere['Users.department_id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $sWhere = null; 
            break;
        } 
        $customer_id = $_GET['customer_id'];
        $sWhere['customer_id'] = $customer_id;

        if ( isset($_GET['name']) && $_GET['name'] != '' )
        {   
            $name = $_GET['name'];
            $sWhere['CustomerImages.name LIKE'] = "%".$name."%";
        }
        if ( isset($_GET['startTime']) && $_GET['startTime'] != '' )
        {
            $startTime = $_GET['startTime'];
            $sWhere['CustomerImages.created >='] = $startTime;
        }
        if ( isset($_GET['endTime']) && $_GET['endTime'] != '' )
        {
            $endTime = $_GET['endTime'];
            $sWhere['CustomerImages.created <='] = $endTime;
        } 


        $this->paginate = [
            'order' => ['created Desc','id Desc'],
            'limit' => '10',
            'conditions' => $sWhere
        ];
        $customerImages = $this->paginate($this->CustomerImages);
        $this->set(compact('customerImages','name','startTime','endTime','customer_id'));
        $this->set('_serialize', ['customerImages']);
        $this->render('view');
    }

    public function add()
    {
        $this->request->allowMethod('post');
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
        $customer_id = $this->request->getData('customer_id');
        $authorized = $this->loadModel('Customers')->findById($customer_id)->contain(['Users'])->where($conditions)->count();
        if (!$authorized) {
            $this->Flash->error(__('无权访问该页面.'));
            return $this->redirect($this->referer());
        }
        
        if ($this->saveImages($this->request->data['images'],$customer_id)) {
            $this->Flash->success(__('图片上传成功.'));
        }else{
            $this->Flash->error(__('图片上传失败，请重试.'));
        }
        $this->updateCustomerModified(['id' => $customer_id]);
        
        return $this->redirect(['controller' => 'CustomerImages','action' => 'index','?' => ['customer_id' => $customer_id]]);
                    
    }



}
