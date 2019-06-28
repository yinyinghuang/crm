<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Mailer\Email;

/**
 * Campaigns Controller
 *
 * @property \App\Model\Table\CampaignsTable $Campaigns
 */
class CampaignsController extends AppController
{
    public $ufo_send_name = 'gc_global@u2.ufosend.com';
    public $ufo_send_key = '51db7766662dfdb2';

    public $mms_account_name = 'gc_global';
    public $mms_api_key = '51db7766662dfdb2';
    public $mms_api_url = "https://api.ufosend.com:8081/v1.0/mms_connect";

    /**
     * Index method
     *
     * @return \Cake\Network\Response|null
     */
    public function index()
    {
        $clear = $this->Campaigns->find('all',[
            'conditions'=> ['created <' => time()-7776000]
        ])->count();   
        $typeArr = ['1' => '短信','2' => '邮件','3' => '彩信','4' => '短信测试','5' => '邮件测试','6' => '彩信测试',];
        $this->set(compact('clear','typeArr'));
        $this->set($_GET);
    }

    /**
     * View method
     *
     * @param string|null $id Campaign id.
     * @return \Cake\Network\Response|null
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        
        $campaign = $this->Campaigns->get($id, [
            'contain' => ['Users','CampaignRecords' => function($q){
                return $q->where(['CampaignRecords.result != ' => 'ok']);
            }]
        ]);
        if ($campaign->type == 1 || $campaign->type == 3) {
            $customer_mobile_table = $this->loadModel('CustomerMobiles')->find();
            $concat = $customer_mobile_table->func()->concat([
                '+',
                'CountryCodes.country_code' => 'identifier',
                '-',
                'CustomerMobiles.mobile' => 'identifier'
            ]);
            foreach ($campaign->campaign_records as $record) {
                $record->type_FK_id && $record->customers = $customer_mobile_table
                    ->contain(['CountryCodes','Customers'])
                    ->select(['name' => 'Customers.name','idtf' => $concat])
                    ->where(['Customers.id in' => explode(',', $record->type_FK_id)])
                    ->hydrate(false)
                    ->toArray();
            }
            if ($campaign->type == 3) {
                $campaign->content = file_get_contents($campaign->content);
                $campaign->image = '/'. $campaign->image;
            }

        } elseif($campaign->type == 2) {
            $customer_email_table = $this->loadModel('CustomerEmails')->find();
            foreach ($campaign->campaign_records as $record) {
                $record->type_FK_id && $record->customers = $customer_email_table
                    ->contain(['Customers'])
                    ->select(['name' => 'Customers.name','idtf' => 'CustomerEmails.email'])
                    ->where(['Customers.id in' => explode(',', $record->type_FK_id)])
                    ->hydrate(false)
                    ->toArray();
            }
        }else{
            foreach ($campaign->campaign_records as $record) {
                $record->customers = [['name' => '测试对象','idtf' => $record->type_FK_id]];
            }
            if ($campaign->type == 6) {
                $campaign->content = file_get_contents($campaign->content);
                $campaign->image =  '/'. $campaign->image;
            }
        }
        $typeArr = ['1' => '短信','2' => '邮件','3' => '彩信','4' => '短信测试','5' => '邮件测试','6' => '彩信测试',];

        $this->set(compact('campaign','typeArr'));
        $this->set('_serialize', ['campaign']);
    }
    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addSms()
    {
        $campaign = $this->Campaigns->newEntity();
        
        $session = $this->request->session();
        if ($this->request->is('post')) { 
            $type = $this->request->getData('type');
            if (in_array($type, ['test','normal'])) {
                $mobileListAll = $mobileId = [];
                if ($type == 'normal') {
                    $customer_ids = $this->filterCustomer('mobile');
                    if (empty($customer_ids)) {
                        $this->Flash->error(__('不存在符合筛选条件的客户'));
                    } else {
                        $campaign->type = 1;
                        $customer_mobile_table = $this->loadModel('CustomerMobiles')->find();
                        $concat = $customer_mobile_table->func()->concat([
                            'CountryCodes.country_code' => 'identifier',
                            '.',
                            'CustomerMobiles.mobile' => 'identifier'
                        ]);
                        $mobileArr = $customer_mobile_table
                            ->select(['mobile' => $concat,'customer_id' => 'CustomerMobiles.customer_id','country_code' => 'CountryCodes.country_code'])
                            ->contain(['CountryCodes','Customers'])
                            ->where(['customer_id in' => $customer_ids,'CountryCodes.country_code' => 852]);

                        
                        foreach ($mobileArr as $key => $m) {
                            if(strlen(trim($m->mobile)) !== 12) continue;
                            $mobileListAll[] = ['mobile' => $m->mobile];
                            $mobileId[$m->mobile] = $m->customer_id;
                            
                        }

                    }
                }else{
                    $mobile = $this->request->getData('number');
                    $code = $this->request->getData('code');
                    $campaign->type = 4;
                    $campaign->remark = '+'. $code . '-' . $mobile;

                    $mobileListAll[] = ['mobile' => $code . '.' . $mobile];
                    $mobileId[$code . '.' . $mobile] = '+'. $code . '-' . $mobile;
                }

                $campaign->content = $this->request->getData('content');
                $campaign->user_id = $this->_user['id'];
                $campaign->success = $campaign->total = $campaign->fail = 0;
                if ($this->Campaigns->save($campaign)) {
                    $this->sendSMS($mobileListAll,$mobileId,$campaign);
                    
                    return $this->redirect(['action' => 'view',$campaign->id]);
                }
                $this->Flash->error(__('短信未成功創建.'));
            }else{
                $this->Flash->error(__('参数不正确，请重试'));
                return $this->redirect(['action' => 'add-sms']);
            }
            
            
        }
        if ($session->read('Campaigns.Customers.filters')) {
            $filters = $session->read('Campaigns.Customers.filters');
            if (isset($filters['event_id'])) {
                $event_id = array_filter(explode(',',$filters['event_id']));
                !empty($event_id) && $event_names = $this->loadModel('Events')->find('list')->where(['id in' => $event_id]);    
            }
            $session->delete('Campaigns.Customers.filters');
        }
        $eventTypes = $this->loadModel('EventTypes')->find('list');
        $this->set(compact('campaign','eventTypes','sendIncorrectReport'));
        $this->set('_serialize', ['campaign']);
        isset($filters) &&$this->set($filters);
        isset($_POST) && $this->set($_POST);
        $this->set(compact('event_names'));

    }

    private function sendSMS($mobileListAll,$mobileId,$campaign)
    {
        set_time_limit(0);
        ignore_user_abort();
        $this->loadModel('CustomerMobiles');
        $this->loadModel('CampaignRecords');
        
        $record = $this->CampaignRecords->query()->insert(['type_FK_id', 'campaign_id', 'result']);
        while (!empty($mobileListAll)) {
            $mobileList500 = array_splice($mobileListAll, 0, 500);
            $result = $this->sendSMSByUFO($campaign->content, $mobileList500);
            
            if ($result->status) {//发送成功
                $sendIncorrectReport = $this->getReport($result->campaign_id);
                foreach ($mobileList500 as $value) {
                    $result = array_key_exists($value['mobile'], $sendIncorrectReport) ? 'Incorrect' : 'ok';
                    $type_FK_id_arr[$result][] = $mobileId[$value['mobile']];
                }
            } else {
                $result = $result->error;
                foreach ($mobileList500 as $value) { 
                    $type_FK_id_arr[$result][] = $mobileId[$value['mobile']]; 
                }
            }

            // $result = 'incorrect_number';
            // foreach ($mobileList500 as $value) { 
            //     $type_FK_id_arr[$result][] = $mobileId[$value['mobile']]; 
            // }
            sleep(1);
        }
            
        foreach ($type_FK_id_arr as $key => $value) {
            $record->values(['type_FK_id' => implode(',', $value), 'campaign_id' => $campaign->id, 'result' => $key]);
            !isset($send) && $send = 1;
        }        
        isset($send) && $record->execute();

        $campaign->success = isset($type_FK_id_arr['ok']) ? count($type_FK_id_arr['ok']) :0;
        $campaign->total= $campaign->total + count($type_FK_id_arr,1)-count($type_FK_id_arr);        
        $campaign->fail = $campaign->total - $campaign->success;
        $this->Campaigns->save($campaign);                
        $this->Flash->success(__('短信發送成功 '. $campaign->success .' 條，失敗 '. $campaign->fail .' 條'));
    }


    protected function sendSMSByUFO($content, $phone)
    {
        $api_url = "https://api.ufosend.com/v2.2/sms_connect.php";

        $meta = array (
               'sms_content' => $content,
               'country_code' => 'MC',  // for multiple countries, please use 'MC', mobile uses 'country_code.XXXX'
               'send_also_ofca_registers' => 1   // '1' means also send to those who already reigstered as in OFCA's Do-Not-Call list
        );
        $meta_json = json_encode($meta);
        $user_list = $phone;

        $user_json =  json_encode($user_list);
        $api_params = array (
            'account_name' => $this->ufo_send_name,
            'api_key' => $this->ufo_send_key,
            'meta_json' => $meta_json,
            'user_json' => $user_json,
        );

        // send request (here use POST) to platform thru API, use curl call 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $api_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        curl_setopt($ch, CURLOPT_TIMEOUT,120); 
        $rm_json = json_decode(curl_exec($ch));   // return message in JSON format
        curl_close($ch);
        
        return $rm_json;
    }

    protected function getReport($campaign_id)
    {
        $api_url = "https://api.ufosend.com/v2.2/sms_connect.php";
        
        $meta = array (
            'get_report' => 'campaign_id',   
            'campaign_id' => $campaign_id,
            'report_type' => 'incorrect_list',
            'row_start' => 1,
            'row_end' => 3000
        );

        // Prepare $meta_json
        $meta_json = json_encode($meta);

        /************************* Make Request to Platform **************************/

        $api_params = array (
            'account_name' => $this->ufo_send_name,
            'api_key' => $this->ufo_send_key,
            'meta_json' => $meta_json,
        );


        // Send request (accepts both GET & POST) to platform thru API, use curl call 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $api_params);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $rm = curl_exec($ch);
        curl_close($ch);

        $obj = json_decode($rm);

        $resp = [];
        if (isset($obj->incorrect_list)) {
            foreach ($obj->incorrect_list as $value) {
                $resp[$value->mobile] = 1;
            }
        }
        
        return $resp;
    }


    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addMms()
    {
        $campaign = $this->Campaigns->newEntity();
        
        $session = $this->request->session();
        if ($this->request->is('post')) {
            $type = $this->request->getData('type');
            if (in_array($type, ['test','normal'])) {
                $mobileListAll = $mobileId = [];
                if ($type == 'normal') {
                    $customer_ids = $this->filterCustomer('mobile');
                    if (empty($customer_ids)) {
                        $this->Flash->error(__('不存在符合筛选条件的客户'));
                    } else {
                        $campaign->type = 3;
                        $mobileArr = $this->loadModel('CustomerMobiles')->find()
                            ->select(['mobile' => 'CustomerMobiles.mobile','customer_id' => 'CustomerMobiles.customer_id'])
                            ->contain(['CountryCodes','Customers'])
                            ->where(['customer_id in' => $customer_ids,'CountryCodes.country_code' => 852]);

                        
                        foreach ($mobileArr as $key => $m) {
                            if(strlen(trim($m->mobile)) !== 8) continue;
                            $mobileListAll[$m->customer_id] = ['mobile' => $m->mobile];
                        }

                    }
                }else{
                    $mobile = $this->request->getData('number');
                    $code = $this->request->getData('code');
                    $campaign->type = 6;
                    $campaign->remark = '+'. $code . '-' . $mobile;

                    $mobileListAll[$campaign->remark] = ['mobile' =>$mobile];
                }

                $content = $this->request->getData('content');
                $mms_campaign_text_url = WWW_ROOT.'files/campaigns/mms_campaign_text_url_'.time().'.txt';
                $mms_campaign_text = fopen($mms_campaign_text_url, 'w');
                fwrite($mms_campaign_text, $content);
                fclose($mms_campaign_text);
                $campaign->content = $mms_campaign_text_url;

                $campaign->subject = $this->request->getData('subject');

                $image = $this->uploadFile(array('png','gif','jpeg','jpg'),'files/campaigns', $this->request->getData('image'));

                if (array_key_exists('url', $image)) {
                    
                    $campaign->image = $image['url']['url'];
                    
                }else{
                    $this->Flash->error(__($image['error']));
                    return $this->redirect(['action' => 'add-mms']);
                }
                $campaign->user_id = $this->_user['id'];
                if ($this->Campaigns->save($campaign)) {
                    $campaign->success = $campaign->total = $campaign->fail = 0;
                    
                    $false = $this->sendMMS($mobileListAll,$campaign);
                    if (!$false) {
                        return $this->redirect(['action' => 'view',$campaign->id]);
                    }else{
                        die;
                    }
                    
                }
                $this->Flash->error(__('彩信未成功創建.'));
            }else{
                $this->Flash->error(__('参数不正确，请重试'));
                return $this->redirect(['action' => 'add-sms']);
            }
            
            
        }
        if ($session->read('Campaigns.Customers.filters')) {
            $filters = $session->read('Campaigns.Customers.filters');
            if (isset($filters['event_id'])) {
                $event_id = array_filter(explode(',',$filters['event_id']));
                !empty($event_id) && $event_names = $this->loadModel('Events')->find('list')->where(['id in' => $event_id]);    
            }
            $session->delete('Campaigns.Customers.filters');
        }
        $eventTypes = $this->loadModel('EventTypes')->find('list');
        $this->set(compact('campaign','eventTypes'));
        $this->set('_serialize', ['campaign']);
        isset($filters) &&$this->set($filters);
        isset($_POST) && $this->set($_POST);
        $this->set(compact('event_names'));
    }

    private function sendMMS($mobileListAll,$campaign)
    {
        set_time_limit(0);
        ignore_user_abort();$false = false;
        $text_file_path = str_replace('\\', '/', $campaign->content);
        $img_file_path = str_replace('\\', '/', WWW_ROOT.$campaign->image);
        // $text_file_path = $campaign->content;
        // $img_file_path = WWW_ROOT.$campaign->image;
        $record = $this->loadModel('CampaignRecords')->query()->insert(['type_FK_id', 'campaign_id', 'result']);
        while (!empty($mobileListAll)) {
            $mobileList = array_values(array_slice($mobileListAll, 0, 500,false));
            $mobileList500 = array_slice($mobileListAll, 0, 500, true);
            $mobileListAll = array_slice($mobileListAll,500,null, true);

            $result = $this->sendMMSByUFO($text_file_path, $img_file_path,$campaign->subject, $mobileList);
            
            if ($result->status) {//发送成功
                
                foreach ($mobileList500 as $key => $value) {                    
                    $type_FK_id_arr['ok'][] = $key;
                }
            } else {
                /*
                object(stdClass) {
                    status => (int) 1
                    return_code => (int) 100
                    data => [
                        (int) 0 => object(stdClass) {
                            mobile => '85297118625'
                            delivery_datetime => '2018-08-16 09:44:35'
                            delivery_status => ''
                        },
                        (int) 1 => object(stdClass) {
                            mobile => '85297803003'
                            delivery_datetime => '2018-08-16 09:44:35'
                            delivery_status => ''
                        }
                    ]
                }*/
                $sendReport = $this->getMMSReport($result->response_code);
                debug('彩信发送错误');
                debug($sendReport);
                $false = true;
                $result = $result->return_code;
                foreach ($mobileList500 as $key => $value) { 
                    $type_FK_id_arr[$result][] = $key; 
                }
            }
            sleep(5);
        }

        foreach ($type_FK_id_arr as $key => $value) {
            $record->values(['type_FK_id' => implode(',', $value), 'campaign_id' => $campaign->id, 'result' => $key]);
            !isset($send) && $send = 1;
        } 
        $send && $record->execute();


        $campaign->success = isset($type_FK_id_arr['ok']) ? count($type_FK_id_arr['ok']) :0;
        $campaign->total= $campaign->total + count($type_FK_id_arr,1)-count($type_FK_id_arr);        
        $campaign->fail = $campaign->total - $campaign->success;

        $this->Campaigns->save($campaign);                
        $this->Flash->success(__('短信發送成功 '. $campaign->success .' 條，失敗 '. $campaign->fail .' 條'));
        return $false;
    }


    protected function sendMMSByUFO($text_file_path, $img_file_path,$subject, $phone)
    {
        /****************************** System Parameters *******************************/

        // account information
        $account_name = $this->mms_account_name;
        $api_key = $this->mms_api_key;
        $api_url = $this->mms_api_url;


        /****************************** Meta Parameters *******************************/

        // compose MMS content

        $boundary = '----=' . md5(uniqid(rand()));

        
        $text_filename = basename($text_file_path);

        $img_filename = basename($img_file_path);

        $content_text = file_get_contents($text_file_path);
        $text_data = $content_text;

        $image_data = file_get_contents($img_file_path);
        $encoded_image = base64_encode($image_data);

debug($text_data);
debug($encoded_image);   
$smil = "
$boundary
Content-Type: application/smil
Content-ID: <mms.smil>
Content-Location: mms.smil

<smil>
<head>
    <layout>
        <root-layout background-color=\"#FF0000\" />
        <region id=\"Image\" top=\"0\" left=\"0\" height=\"50%\" width=\"100%\" fit=\"fill\"/>
        <region id=\"Text\" top=\"50%\" left=\"0\" height=\"50%\" width=\"100%\" fit=\"fill\"/>
    </layout>
</head>
<body>
    <par dur=\"13000ms\">
        <text src=\"$text_filename\" region=\"Text\" begin=\"1000ms\" end=\"9000ms\"></text>
        <img src=\"$img_filename\" region=\"Image\"></img>
    </par>
</body>
</smil>

--$boundary
Content-Type: text/plain; charset=UTF-8; name=$text_filename
Content-Transfer-Encoding: 7bit
Content-Disposition: attachment;FileName=$text_filename;Charset=UTF-8
Content-ID: <$text_filename>
Content-Location: $text_filename

$text_data

--$boundary
Content-Type: image/jpeg;
Content-Transfer-Encoding: base64
Content-Disposition: attachment;FileName=$img_filename;Charset=UTF-8
Content-ID: <$img_filename>
Content-Location: $img_filename

$encoded_image
--$boundary--
";


        // define meta json – this is the information required to instruct how ufo platform handles your campaign 
        $meta = array (
            'boundary' => $boundary,
            'subject' => $subject,
            'smil' => $smil,
            'country_code' => '852',     // only support send to HK number
        );


        $meta_json = json_encode($meta);


        /****************************** User Parameters *******************************/

        // define user json – this is a set of users who will be receiving the MMS, no. of users can be 1 to 500
        $user_list = array();

        // json encode 
        $user_json =  json_encode($phone);


        /************************* Make Request to Platform **************************/

        // prepare URL 
        $api_params = array (
            'account_name' => $account_name,
            'api_key' => $api_key,
            'meta_json' => $meta_json,
            'user_json' => $user_json,
        );

        // send request (here use POST) to platform thru API, use curl call 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_TIMEOUT,120); 

        $rm_json = curl_exec($ch);   // return message in JSON format

        /******************************* Show Result ********************************/
        curl_close($ch);
  
        return json_decode($rm_json);
    }
    protected function getMMSReport($response_code)
    {
        /****************************** System Parameters *******************************/

        // account information
        $account_name = $this->mms_account_name;
        $api_key = $this->mms_api_key;
        $api_url = $this->mms_api_url;


        /****************************** Meta Parameters *******************************/


        // define meta json – this is the information required to instruct how ufo platform handles your campaign 
        $meta = array (
            'get_report' => 1,
            'response_code' => $response_code
        );


        $meta_json = json_encode($meta);

        /************************* Make Request to Platform **************************/

        // prepare URL 
        $api_params = array (
            'account_name' => $account_name,
            'api_key' => $api_key,
            'meta_json' => $meta_json,
        );

        // send request (here use POST) to platform thru API, use curl call 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($api_params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); 
        curl_setopt($ch, CURLOPT_TIMEOUT,120); 
        $rm_json = curl_exec($ch);   // return message in JSON format
        curl_close($ch);


        /******************************* Show Result ********************************/

        // show the return result
        return json_decode($rm_json);
    }


    /**
     * Add method
     *
     * @return \Cake\Network\Response|null Redirects on successful add, renders view otherwise.
     */
    public function addEmail()
    {
        $campaign = $this->Campaigns->newEntity();        
        $session = $this->request->session();

        if ($this->request->is('post')) {

            $type = $this->request->getData('type');
            if (in_array($type, ['test','normal'])) {
                
                if ($type == 'normal') {
                    $campaign->type = 2;
                     $customer_ids = $this->filterCustomer('email');
                    if (empty( $customer_ids)) {
                        $this->Flash->error(__('不存在符合筛选条件的客户'));
                    } else {                       
                       $emailList = $this->loadModel('CustomerEmails')->find('all',[
                           'group' => ['email'],
                           'conditions' => ['customer_id in' => $customer_ids]
                       ])->combine('customer_id','email')->toArray();
                    }
                }else{
                    $campaign->type = 5;
                    $recipients =  $campaign->remark = $this->request->getData('recipient');
                    $recipients = explode(';', $recipients);
                    foreach ($recipients as $recipient) {
                        $emailList[$recipient] = $recipient;
                    }                   

                }
                
                $campaign->subject = $this->request->getData('subject');
                $campaign->content = $this->save_local($this->request->getData('content'));
                $campaign->user_id = $this->_user['id'];
                if ($this->Campaigns->save($campaign)) {
                    $campaign->success = $campaign->total = $campaign->fail = 0;
                    $this->sendEmail($emailList,$campaign);
                    
                    return $this->redirect(['action' => 'view',$campaign->id]);                  
                }else{
                    $this->Flash->error(__('郵件未成功創建.'));
                }
            }else{
                $this->Flash->error(__('参数不正确，请重试'));
                return $this->redirect(['action' => 'add-email']);
            }
        }
        if ($session->read('Campaigns.Customers.filters')) {
            $filters = $session->read('Campaigns.Customers.filters');
            if (isset($filters['event_id'])) {
                $event_id = array_filter(explode(',',$filters['event_id']));
                !empty($event_id) && $event_names = $this->loadModel('Events')->find('list')->where(['id in' => $event_id]);    
            }
            $session->delete('Campaigns.Customers.filters');
        }
        $eventTypes = $this->loadModel('EventTypes')->find('list');
        $this->set(compact('campaign','eventTypes','type'));
        $this->set('_serialize', ['campaign']);
        isset($filters) &&$this->set($filters);
        isset($_POST) && $this->set($_POST);
        $this->set(compact('event_names'));
    }
    protected function sendEmail($emailList, $campaign)
    {
        set_time_limit(0);                    
        ignore_user_abort();
        $this->loadModel('CampaignRecords');

        $record = $this->CampaignRecords->query()->insert(['type_FK_id', 'campaign_id', 'result']);

        while (!empty($emailList)) {
            $emailList25 = array_slice($emailList, 0, 100, true);
            $emailList = array_slice($emailList, 100,null, true);
            $result = $this->sendEmailByPHPEmail($emailList25, $campaign->subject, $campaign->content);
            if ($result == 'ok') {
                $campaign->success += count($emailList25);
                $type_FK_id_arr['ok'] = isset($type_FK_id_arr['ok']) ? array_merge($type_FK_id_arr['ok'],array_keys($emailList25)):array_keys($emailList25);
            } else {

                $campaign->fail += count($emailList25);
                 $type_FK_id_arr[$result] = isset($type_FK_id_arr[$result]) ? array_merge($type_FK_id_arr[$result],array_keys($emailList25)):array_keys($emailList25);
            }
            sleep(5);

        }
        $campaign->total = $campaign->fail+$campaign->success;
        $this->Campaigns->save($campaign);                    
        foreach ($type_FK_id_arr as $key => $value) {
            $record->values(['type_FK_id' => implode(',', $value), 'campaign_id' => $campaign->id, 'result' => $key]);
            !isset($send) && $send = 1;
        }        
        isset($send) && $record->execute();                
        $this->Flash->success(__('郵件發送完成.'));
    }

    protected function sendEmailByPHPEmail($recipients, $subject, $content)
    {       
        require_once(ROOT . DS  . 'vendor' . DS  . 'PHPMailer' . DS . 'class.phpmailer.php');
        require_once(ROOT . DS  . 'vendor' . DS  . 'PHPMailer' . DS . 'class.smtp.php');
        $send_mail = json_decode($this->loadModel('Configs')->findByName('send_mail')->first()->value,true);
        $mail  = new \PHPMailer(); 

        $mail->CharSet    ="utf-8";                 
        $mail->IsSMTP();                          
        $mail->SMTPAuth   = true;                 
        $mail->SMTPSecure = "ssl";                
        $mail->Host       = $send_mail['host'];      
        $mail->Port       = $send_mail['port'];                   
        $mail->Username   = $send_mail['address']; 
        $mail->Password   = $send_mail['psw'];        

        $mail->Subject = $subject;
        $mail->SetFrom($send_mail['address'], $send_mail['name']);
        $mail->AddAddress($send_mail['address'], '');
        foreach ($recipients as $recipient) {
            $mail->addBCC($recipient);
        }

        // get all img tags
        preg_match_all('/<img.*?>/', $content, $matches);
        if (isset($matches[0])){
            // foreach tag, create the cid and embed image
            $i = 1;
            foreach ($matches[0] as $img)
            {
                // make cid
                $id = 'img'.($i++);
                // replace image web path with local path
                preg_match('/src=\"(.*?)"/', $img, $m);
                if (!isset($m[1])) continue;
                // add
                $m[1] = substr(str_replace('/', DS, $m[1]), 1); 
                $s = $mail->AddEmbeddedImage(WWW_ROOT.$m[1], $id );
                $img_replace = preg_replace("/src=([\"|']?)([^ \"'>]+)\\1/i", 'src="cid:'.$id.'"', $img);
                $content = str_replace($img, $img_replace, $content); 
            }
        }
        $mail->MsgHTML($content);
        
        $res = $mail->Send();
        // debug($res);
        // die;
        if ($res) {
            $res = 'ok';
        } else {
            $res = str_replace(PHP_EOL, ' ', $mail->ErrorInfo);
        }
        return $res;
    }

    /**
     * Delete method
     *
     * @param string|null $id Campaign id.
     * @return \Cake\Network\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $campaign = $this->Campaigns->get($id);
        if ($this->Campaigns->delete($campaign)) {

            $this->Flash->success(__('删除成功.'));
        } else {
            $this->Flash->error(__('删除失败. 请重试.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function clear()
    {
        $this->request->allowMethod(['post', 'delete']);
        $conditions['created < '] = date('Y-m-d H:i:s', time()-7776000);

        $results = $this->Campaigns->find('all',[
            'conditions' => $conditions,
            'fields' => 'id'
        ]);
        $campaignIds = [];
        foreach ($results as $value) {
            $campaignIds[] = $value->id;
        }

        $number = 0;
        
        if ($campaignIds) {
            $this->Campaigns->CampaignRecords->deleteAll(['campaign_id in ' => $campaignIds]);
            $number = $this->Campaigns->deleteAll($conditions);
        }        
        $this->Flash->success(__('The '.$number.' campaigns has been deleted.'));

        return $this->redirect(['action' => 'index']);
    }

    public function resend($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $record = $this->loadModel('CampaignRecords')->get($id,[
            'contain' => ['Campaigns']
        ]);
        switch ($record->campaign->type) {
            case 1:
            case 4:
                $mobileListAll = $mobileId = [];
                if ($record->campaign->type == 1) {
                    $customer_ids = explode(',', $record->type_FK_id);
                    
                    $customer_mobile_table = $this->loadModel('CustomerMobiles')->find();
                    $concat = $customer_mobile_table->func()->concat([
                        'CountryCodes.country_code' => 'identifier',
                        '.',
                        'CustomerMobiles.mobile' => 'identifier'
                    ]);
                    $mobileArr = $customer_mobile_table
                        ->select(['mobile' => $concat,'customer_id' => 'CustomerMobiles.customer_id','country_code' => 'CountryCodes.country_code'])
                        ->contain(['CountryCodes','Customers'])
                        ->where(['customer_id in' => $customer_ids]);                    
                    foreach ($mobileArr as $key => $m) {
                        if ($m->country_code == '86') continue; 
                        $mobileListAll[] = ['mobile' => $m->mobile];
                        $mobileId[$m->mobile] = $m->customer_id;
                        
                    }                   
                }else{
                    $mobile = str_replace('-', '.', substr($record->campaign->remark,1));
                    $mobileListAll[] = ['mobile' => $mobile];
                    $mobileId[$mobile] = $record->campaign->remark;
                }
                $record->campaign->total = $record->campaign->total-count($mobileId);                 
                $record->campaign->fail = $record->campaign->fail-count($mobileId);                                 
                $this->sendSms($mobileListAll,$mobileId,$record->campaign);
                $this->CampaignRecords->deleteAll(['id' => $id]);
                return $this->redirect(['action' => 'view',$record->campaign->id]);
            case 2:
            case 5:
                if ($record->campaign->type == 2) {
                    $customer_ids = explode(',', $record->type_FK_id);
                    $emailList = $this->loadModel('CustomerEmails')->find('all',[
                       'group' => ['email'],
                       'conditions' => ['customer_id in' => $customer_ids]
                    ])->combine('customer_id','email')->toArray();
                }else{
                    $recipients = explode(';', $record->campaign->remark);
                    foreach ($recipients as $recipient) {
                        $emailList[$recipient] = $recipient;
                    }
                }   
                $record->campaign->total = $record->campaign->total-count($emailList);                 
                $record->campaign->fail = $record->campaign->fail-count($emailList); 

                $this->sendEmail($emailList,$record->campaign);
                $this->CampaignRecords->deleteAll(['id' => $id]);
                return $this->redirect(['action' => 'view',$record->campaign->id]);
        }
        
        return $this->redirect(['action' => 'view', $record->campaign->id]);
    }

    public function ajaxList() 
    {
        $campaigns = $this->filter();
        $typeArr = ['1' => '短信','2' => '邮件','3' => '彩信','4' => '短信测试','5' => '邮件测试','6' => '彩信测试',];
        $this->set(compact('campaigns','typeArr'));
    }

    private function filter($type = '') 
    {
        $conditions = [];
        if ( isset($_GET['name']) && $_GET['name'] != '' )
        {   
            $name = $_GET['name'];
            $conditions['Users.username LIKE'] = "%".$name."%";
        }
        if ( isset($_GET['type']) && !empty($_GET['type']))
        {
            $type = $_GET['type'];
            $conditions['Campaigns.type in'] = $type;
        }
        if ( isset($_GET['startTime']) && $_GET['startTime'] != '' )
        {
            $startTime = $_GET['startTime'];
            $conditions['Campaigns.created >='] = $startTime;
        }
        if ( isset($_GET['endTime']) && $_GET['endTime'] != '' )
        {
            $endTime = $_GET['endTime'];
            $conditions['Campaigns.created <='] = $endTime;
        }

        if ($type == 'all') {
            $offset = $limit = null;
        }else{
            $limit = 20;
            $offset = isset($_GET['page'])&& intval($_GET['page'])>0 ? ($_GET['page']-1) *20 : 0;
        }

        $campaigns = $this->Campaigns->find('all',[
            'contain' => ['Users'],
            'order' => ['Campaigns.created Desc'],
            'limit' => $limit,
            'offset' => $offset,
            'conditions' => $conditions
        ]); 

        return $campaigns;
    }

    public function bulk()
    {
        $this->request->allowMethod(['post', 'delete']);
        $request = $this->request->getData();
        if(!empty($request['ids'])){
            switch ($request['submit']) {
                case 'del':
                    $this->Campaigns->deleteAll(['id in' => $request['ids']]);
                    $this->Campaigns->CampaignRecords->deleteAll(['campaign_id in' => $request['ids']]);
                    $this->Flash->success(__('删除成功.'));
                    break;
            }
        }        
        return $this->redirect(['action' => 'index']);
    }



    protected function save_local($content)
    {
        if($content == '<br type="_moz" />') return '';//FireFox
        if($content == '&nbsp;') return '';//Chrome
        $content = preg_replace("/allowScriptAccess=\"always\"/i", "", $content);
        $content = preg_replace("/allowScriptAccess/i", "allowscr-iptaccess", $content);
        if(!preg_match_all("/src=([\"|']?)([^ \"'>]+)\\1/i", $content, $matches)) return $content;
        foreach($matches[2] as $k => $url) {
            // if(is_crsf($url)) $content = str_replace($url, DT_SKIN.'image/nopic.gif', $content);
        }
        if(strpos($content, 'data:image') === false) return $content;
       
        $urls = $oldpath = $newpath = array();
        foreach($matches[2] as $url) {
            if(in_array($url, $urls)) continue;
            $urls[$url] = $url;
            if(strpos($url, 'data:image') === false) continue;
            if(strpos($url, ';base64,') === false) continue;
            $t1 = explode(';base64,', $url);
            $t2 = explode('/', $t1[0]);
            $file_ext = $t2[1];
            in_array($file_ext, array('jpg', 'gif', 'png')) or $file_ext = 'jpg';
            $filedir = 'files' . DS . 'upload' . DS. date('Ymd').''.DS.'';
            $filepath = '/files/upload/'. date('Ymd').'/';
            
            $fileroot = WWW_ROOT.  $filedir;
            $filename = date('Ymd').mt_rand(10, 99).'.'.$file_ext;
            $newfile = $fileroot.$filename;
            if(!$this->is_image($newfile)) continue;

            if (!is_dir($fileroot)) {
                mkdir($fileroot);
            }
            if(file_put_contents($newfile, base64_decode($t1[1]))) {               

                $oldpath[] = $url;

                $newpath[] = $filepath . $filename;
            }
        }
        unset($matches);
        return str_replace($oldpath, $newpath, $content);
    }
    protected function is_image($file='')
    {
        return preg_match("/^(jpg|jpeg|gif|png|bmp)$/i", $this->file_ext($file));
    }
    protected function file_ext($filename = '') 
    {
        if(strpos($filename, '.') === false) return '';
        $ext = strtolower(trim(substr(strrchr($filename, '.'), 1)));
        return preg_match("/^[a-z0-9]{1,10}$/", $ext) ? $ext : '';
    }

    /**
     * 根据条件筛选
     * @return $customers
     */
    private function filterCustomer($type) 
    {
        $conditions = null;
        $this->loadModel('Customers');
        $this->loadModel('Businesses');
        switch ($this->_user['role_id']) {
            case 1:
                $conditions['Users.id'] = $this->_user['id']; 
            break;
            case 2:
                $conditions['Users.role_id <= '] = $this->_user['role_id'];
                $conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
        }  

        if ( isset($_POST['customer_id']) && $_POST['customer_id'] != '' )
        {   
            $customer_id = $_POST['customer_id'];
            $conditions['Customers.id'] = $customer_id;
        }
        if ( isset($_POST['search_id']) && $_POST['search_id'] != '' )
        {   
            $search_id = $_POST['search_id'];
            $conditions['Customers.id'] = $search_id;
        }

        if ( isset($_POST['user_id']) && $_POST['user_id'] != '' )
        {   
            $user_id = $_POST['user_id'];
            $conditions['Customers.user_id'] = $user_id;
        }

        if ( isset($_POST['customer_name']) && $_POST['customer_name'] != '' )
        {   
            $customer_name = $_POST['customer_name'];
            $conditions['Customers.name LIKE'] = "%".$customer_name."%";
        }
        if ( isset($_POST['source']) && $_POST['source'] != '' )
        {
            $source = $_POST['source'];
            $conditions['Customers.source LIKE'] = "%".$source."%";
        }
        if ( isset($_POST['startTime']) && $_POST['startTime'] != '' )
        {
            $startTime = $_POST['startTime'];
            $conditions['Customers.modified >='] = $startTime;
        }
        if ( isset($_POST['endTime']) && $_POST['endTime'] != '' )
        {
            $endTime = $_POST['endTime'];
            $conditions['Customers.modified <='] = $endTime;
        } 
        if ( isset($_POST['partedStartTime']) && $_POST['partedStartTime'] != '' )
        {
            $partedStartTime = $_POST['partedStartTime'];
            $conditions['Businesses.parted >='] = $partedStartTime;
        }  
        if ( isset($_POST['partedEndTime']) && $_POST['partedEndTime'] != '' )
        {
            $partedEndTime = $_POST['partedEndTime'];
            $conditions['Businesses.parted <='] = $partedEndTime;
        }  
        if ( isset($_POST['event_id']) && $_POST['event_id'] != ',' )
        {

            $event_id = array_filter(explode(',',$_POST['event_id']));
            !empty($event_id) && $conditions['Businesses.event_id in'] = $event_id;            

        }        
        if ( isset($_POST['mobile']) && $_POST['mobile'] != '' )
        {
            $mobile = $_POST['mobile'];
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

        if ( isset($_POST['email']) && $_POST['email'] != '' )
        {
            $email = $_POST['email'];
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
        
        if ( isset($_POST['state']) && !empty($_POST['state']))
        {
            $conditions['Businesses.state in'] = $_POST['state'];
        }
        if ( isset($_POST['sort']) && $_POST['sort'] != '' && isset($_POST['direction']) && $_POST['direction'] != '' )
        {
            array_unshift($order, 'Customers.'.$_POST['sort'] . ' '. $_POST['direction']);
        }

        $limit = null;
        
        $this->_advanced = [];
        if (isset($_POST['advanced'])) {
            foreach ($_POST['advanced'] as $key => $value) {
                $value['num'] && $this->_advanced[$key] = $value;
            }
        }
        $containArr = [
            'mobile' =>['CustomerMobiles' => function($q){
                return $q->contain(['CountryCodes']);
            }],
            'email' => ['CustomerEmails']
        ];
        $this->_contain = $containArr[$type];
        if (!empty($this->_advanced)) {
            $case_fileds = [];
            foreach ($this->_advanced as $key => $value) {
                $case_fileds['type' . $key] = 'SUM(CASE WHEN Businesses.event_type_id='.$key.' THEN 1 else 0 END)';
            }
            $businesses = $this->Customers->Businesses->find()
                ->where($conditions)
                ->select($case_fileds)
                ->enableAutoFields(true)
                ->contain(['Customers','Users'])
                ->group(['Businesses.customer_id'])
                ->limit($limit)
                ->hydrate(false)
                ->filter(function($row)
                {
                    $res = true;
                    foreach ($this->_advanced as $key => $value) {
                        $attr = 'type' . $key;
                        switch ($value['rel']) {
                            case 'lt':
                                $res = $res && $row->$attr < $value['num']; 

                                break;
                            case 'gt':
                                $res = $res && $row->$attr > $value['num']; 
                                break;
                            case 'eq':
                                $res = $res && $row->$attr == $value['num']; 
                                break;
                        }
                        
                    }
                    return $res;
                })
                ->toArray();
            $customer_ids = [];
            if (!empty($businesses)) {
                $customer_ids = array_column(array_column($businesses, 'customer'), 'id');
            }

        }else{

            $customers = $this->Customers->find('all',[
                'contain' => ['Users'] + $this->_contain,
                'limit' => $limit,
                'conditions' => $conditions
            ])
            ->leftJoin('Businesses','Businesses.customer_id=Customers.id')
            ->hydrate(false)
            ->toArray();
            $customer_ids = array_column($customers, 'id');
        }
        return $customer_ids;
    }
    
}
