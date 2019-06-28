<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Routing\Router;
use Cake\I18n\Date;

class StatisticsController extends AppController
{
    public function index()
    {
        $this->_conditions=[];
        switch ($this->_user['role_id']) {
            case 2:
                $this->_conditions['Users.role_id <= '] = $this->_user['role_id'];
                $this->_conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $this->_conditions = []; 
            break;
        }
        
        $this->loadModel('Businesses');
    	$this->loadModel('Customers');
        $codes = $this->loadModel('CountryCodes')->find()->combine('id','country_code')->toArray();
        
        $cur_month = $this->Businesses->find()->func()->date_format([
            'Businesses.created' => 'identifier',
            "'%Y%m'" => 'literal'
        ]);
        $cur_month_customer = $this->Customers->find()->func()->date_format([
            'Customers.created' => 'identifier',
            "'%Y%m'" => 'literal'
        ]);
        $conditions[date('Ym')] = $cur_month;
        $conditions = array_merge($conditions,$this->_conditions);
        $conditions_customer[date('Ym')] = $cur_month_customer;
        $conditions_customer = array_merge($conditions_customer,$this->_conditions);

        /**
         * 业务员订单数据
         * @var [type]
         */
        $userBusinessSignedMonth = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($conditions + ['Businesses.state' => 2])
            ->contain(['Users'])
            ->limit(null);

        $userBusinessSignedHistory = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where(['Businesses.state' => 2])
            ->contain(['Users'])
            ->limit(null);

        $userBusinessNewMonth = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($conditions)
            ->contain(['Users'])
            ->limit(null);

        $userBusinessIng = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where(['Businesses.state' => 0])
            ->contain(['Users'])
            ->limit(null);

        /**
         * 业务员客户数据
         * @var [type]
         */
        $userCustomerNewMonth = $this->Customers->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Customers.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($conditions_customer)
            ->contain(['Users'])
            ->limit(null);


        $userCustomerNewHistory = $this->Customers->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Customers.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($this->_conditions)
            ->contain(['Users'])
            ->limit(null);

        /**
         * 客户订单数据
         * @var [type]
         */
        $customerPartedTop10Month = $this->Businesses->find()
            ->select(['customerid' => 'Customers.id','name' => 'Customers.name',"total"=>"COUNT(Businesses.id)"])
            ->enableAutoFields(true)
            ->order(['total DESC'])
            ->group(['customerid'])
            ->where($conditions)
            ->contain(['Customers' => function($q){
                return $q->contain(['CustomerMobiles']);
            },'Users'])
            ->limit(10);
        $customerIngTop10 = $this->Businesses->find()
            ->select(['customerid' => 'Customers.id','name' => 'Customers.name',"total"=>"COUNT(Businesses.id)"])
            ->enableAutoFields(true)
            ->order(['total DESC'])
            ->group(['customerid'])
            ->where(['Businesses.state' => 0])
            ->contain(['Customers' => function($q){
                return $q->contain(['CustomerMobiles']);
            },'Users'])
            ->limit(10);

        $customerSignedTop10Month = $this->Businesses->find()
            ->select(['customerid' => 'Customers.id','name' => 'Customers.name',"total"=>"COUNT(Businesses.id)"])
            ->enableAutoFields(true)
            ->order(['total DESC'])
            ->group(['customerid'])
            ->where($conditions + ['Businesses.state' => 2])
            ->contain(['Customers' => function($q){
                return $q->contain(['CustomerMobiles']);
            },'Users'])
            ->limit(10);





        $newBusinessByMonth = $newCustomerByMonth = [];
        $month = date('m');
        $month_lately_arr = [$month-5,$month-4,$month-3,$month-2,$month-1,$month-0];


        $customerData = $this->Customers->find()
            ->select(['month' => 'MONTH(Customers.created)','total' => 'count(Customers.id)'])
            ->where(['Customers.created >=' => new Date('-6 months')])
            ->group(['month'])
            ->order('month ASC')
            ->contain(['Users']);
        foreach ($customerData as $value) {
            $newCustomerByMonth[$value->month]['total'] = $value->total;
        }


        $businessSingedOrClosedData = $this->Businesses->find()
            ->select([
                'month' => 'MONTH(Businesses.created)',
                'signed' => 'SUM(CASE WHEN Businesses.state=2 THEN 1 else 0 END)',
                'closed' => 'SUM(CASE WHEN Businesses.state=1 THEN 1 else 0 END)'
            ])
            ->where(['Businesses.modified >=' => new Date('-6 months')])
            ->group(['month'])
            ->order('month ASC')
            ->contain(['Users']);
        foreach ($businessSingedOrClosedData as $value) {
            $newBusinessByMonth[$value->month]['signed'] = $value->signed;
            $newBusinessByMonth[$value->month]['closed'] = $value->closed;
        }

        $businessTotalData = $this->Businesses->find()
            ->select([
                'month' => 'MONTH(Businesses.created)',
                'total' => 'SUM(1)'
            ])
            ->where(['Businesses.created >=' => new Date('-6 months')])
            ->group(['month'])
            ->order('month ASC')
            ->contain(['Users']);

        foreach ($businessTotalData as $value) {
            $newBusinessByMonth[$value->month]['total'] = $value->total;
        }

        $labelArr = ['total' => ['新增总数','black'],'signed' => ['新增成交','green'],'closed' => ['新增失败','red']];

        $this->set(compact('userBusinessSignedMonth','customerPartedTop10Month','customerIngTop10','codes','customerSignedTop10Month','userBusinessNewMonth','userBusinessIng','userBusinessSignedHistory','userCustomerNewMonth','userCustomerNewHistory','newCustomerByMonth','newBusinessByMonth','labelArr','month_lately_arr'));

    }

    public function sendReporter(){
        date_default_timezone_set('PRC');

       
        $exe = WWW_ROOT . 'js' . DS  . 'vendors' . DS  . 'phantomjs' . DS  . 'phantomjs';
       
        $capture_script = WWW_ROOT . 'js' . DS  . 'vendors' . DS  . 'phantomjs' . DS  . 'capture.js';
        $reporterUrl = Router::url(['controller' => 'Statistics','action' => 'draw'], true);

        $today =  date('y-m-d');
        $img_position = WWW_ROOT . 'img' . DS  . 'dailyReporter' . DS  . 'daily_reporter_' . $today .'.png';
        $result = exec($exe . ' ' . $capture_script .' ' . $reporterUrl . ' ' . $img_position,$out,$ret);
        $this->set('result',$result);
        $receive_reporter_email_address = $this->Configs
            ->findByName('receive_reporter_email_address')->first()->value;            
        
        $sendToArray =  explode(";",$receive_reporter_email_address);
        
        require_once(ROOT . DS  . 'vendor' . DS  . 'PHPMailer' . DS . 'class.phpmailer.php');
        require_once(ROOT . DS  . 'vendor' . DS  . 'PHPMailer' . DS . 'class.smtp.php');
        $mail  = new \PHPMailer(); 

        $mail->CharSet    ="utf-8";                 
        $mail->IsSMTP();                            
        $mail->SMTPAuth   = true;                 
        $mail->SMTPSecure = "ssl";                
        $mail->Host       = "smtp.qq.com";      
        $mail->Port       = 465;                   
        $mail->Username   = "1937720180@qq.com"; 
        $mail->Password   = "huihui!123";        
        $mail->SetFrom('1937720180@qq.com', "CRM");   
                                                    
        $mail->Subject = "月报";      

        foreach ($sendToArray as $key => $value) {
            $mail->AddAddress($value, $store_name);
        }

        $cid = "monthly_report_".$today;
        $mail->AddEmbeddedImage($img_position, $cid);
        $emailContent = '<h1>'.$today.'月报</h1>
        <div>查看日報鏈接'.Router::url(['controller' => 'Statistics','action' => 'index'],true).'</div>
        <div><img src="cid:'.$cid.'" alt="report"/></div>';
        $mail->MsgHTML( $emailContent);
        
        // $res = $mail->Send();
        if(!$res){
            $res = str_replace(PHP_EOL, ' ', "Mailer Error: ".$mail->ErrorInfo);
        }else{
            $res = 'Reporter has been sent.';
        }
        $this->set('phantomjs_code',$result);
        $this->set('email_rslt',$res);       
    }

    public function draw()
    {
        $this->_conditions=[];
        switch ($this->_user['role_id']) {
            case 2:
                $this->_conditions['Users.role_id <= '] = $this->_user['role_id'];
                $this->_conditions['Users.department_id'] = $this->_user['department_id']; 
            break;
            case 3:
            case 4:
                $this->_conditions = []; 
            break;
        }
        
        $this->loadModel('Businesses');
        $this->loadModel('Customers');
        $codes = $this->loadModel('CountryCodes')->find()->combine('id','country_code')->toArray();
        
        $cur_month = $this->Businesses->find()->func()->date_format([
            'Businesses.created' => 'identifier',
            "'%Y%m'" => 'literal'
        ]);
        $cur_month_customer = $this->Customers->find()->func()->date_format([
            'Customers.created' => 'identifier',
            "'%Y%m'" => 'literal'
        ]);
        $conditions[date('Ym')] = $cur_month;
        $conditions = array_merge($conditions,$this->_conditions);
        $conditions_customer[date('Ym')] = $cur_month_customer;
        $conditions_customer = array_merge($conditions_customer,$this->_conditions);

        /**
         * 业务员订单数据
         * @var [type]
         */
        $userBusinessSignedMonth = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($conditions + ['Businesses.state' => 2])
            ->contain(['Users'])
            ->limit(null);

        $userBusinessSignedHistory = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where(['Businesses.state' => 2])
            ->contain(['Users'])
            ->limit(null);

        $userBusinessNewMonth = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($conditions)
            ->contain(['Users'])
            ->limit(null);

        $userBusinessIng = $this->Businesses->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Businesses.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where(['Businesses.state' => 0])
            ->contain(['Users'])
            ->limit(null);

        /**
         * 业务员客户数据
         * @var [type]
         */
        $userCustomerNewMonth = $this->Customers->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Customers.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($conditions_customer)
            ->contain(['Users'])
            ->limit(null);


        $userCustomerNewHistory = $this->Customers->find()
            ->select(['userid' => 'Users.id','username' => 'Users.username',"total_signed"=>"COUNT(Customers.id)"])
            ->order(['total_signed DESC'])
            ->group(['userid'])
            ->where($this->_conditions)
            ->contain(['Users'])
            ->limit(null);

        /**
         * 客户订单数据
         * @var [type]
         */
        $customerPartedTop10Month = $this->Businesses->find()
            ->select(['customerid' => 'Customers.id','name' => 'Customers.name',"total"=>"COUNT(Businesses.id)"])
            ->enableAutoFields(true)
            ->order(['total DESC'])
            ->group(['customerid'])
            ->where($conditions)
            ->contain(['Customers' => function($q){
                return $q->contain(['CustomerMobiles']);
            },'Users'])
            ->limit(10);
        $customerIngTop10 = $this->Businesses->find()
            ->select(['customerid' => 'Customers.id','name' => 'Customers.name',"total"=>"COUNT(Businesses.id)"])
            ->enableAutoFields(true)
            ->order(['total DESC'])
            ->group(['customerid'])
            ->where(['Businesses.state' => 0])
            ->contain(['Customers' => function($q){
                return $q->contain(['CustomerMobiles']);
            },'Users'])
            ->limit(10);

        $customerSignedTop10Month = $this->Businesses->find()
            ->select(['customerid' => 'Customers.id','name' => 'Customers.name',"total"=>"COUNT(Businesses.id)"])
            ->enableAutoFields(true)
            ->order(['total DESC'])
            ->group(['customerid'])
            ->where($conditions + ['Businesses.state' => 2])
            ->contain(['Customers' => function($q){
                return $q->contain(['CustomerMobiles']);
            },'Users'])
            ->limit(10);





        $newBusinessByMonth = $newCustomerByMonth = [];
        $month = date('m');
        $month_lately_arr = [$month-5,$month-4,$month-3,$month-2,$month-1,$month-0];


        $customerData = $this->Customers->find()
            ->select(['month' => 'MONTH(Customers.created)','total' => 'count(Customers.id)'])
            ->where(['Customers.created >=' => new Date('-6 months')])
            ->group(['month'])
            ->order('month ASC')
            ->contain(['Users']);
        foreach ($customerData as $value) {
            $newCustomerByMonth[$value->month]['total'] = $value->total;
        }


        $businessSingedOrClosedData = $this->Businesses->find()
            ->select([
                'month' => 'MONTH(Businesses.created)',
                'signed' => 'SUM(CASE WHEN Businesses.state=2 THEN 1 else 0 END)',
                'closed' => 'SUM(CASE WHEN Businesses.state=1 THEN 1 else 0 END)'
            ])
            ->where(['Businesses.modified >=' => new Date('-6 months')])
            ->group(['month'])
            ->order('month ASC')
            ->contain(['Users']);
        foreach ($businessSingedOrClosedData as $value) {
            $newBusinessByMonth[$value->month]['signed'] = $value->signed;
            $newBusinessByMonth[$value->month]['closed'] = $value->closed;
        }

        $businessTotalData = $this->Businesses->find()
            ->select([
                'month' => 'MONTH(Businesses.created)',
                'total' => 'SUM(1)'
            ])
            ->where(['Businesses.created >=' => new Date('-6 months')])
            ->group(['month'])
            ->order('month ASC')
            ->contain(['Users']);

        foreach ($businessTotalData as $value) {
            $newBusinessByMonth[$value->month]['total'] = $value->total;
        }

        $labelArr = ['total' => ['新增总数','black'],'signed' => ['新增成交','green'],'closed' => ['新增失败','red']];

        $this->set(compact('userBusinessSignedMonth','customerPartedTop10Month','customerIngTop10','codes','customerSignedTop10Month','userBusinessNewMonth','userBusinessIng','userBusinessSignedHistory','userCustomerNewMonth','userCustomerNewHistory','newCustomerByMonth','newBusinessByMonth','labelArr','month_lately_arr'));

    }
}