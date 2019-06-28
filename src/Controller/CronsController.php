<?php

namespace App\Controller;

use App\Controller\AppController;
use Cake\Event\Event;
use Cake\I18n\Date;
use Cake\I18n\Time;
use Cake\Network\Http\Client;
use Cake\Controller\Component\CorpWechatComponent;

class CronsController extends AppController
{
    public function beforeFilter(Event $event)
    {
        parent::beforeFilter($event);
        $this->loadComponent('CorpWechat');
        $this->Auth->allow(); 
    }

    public function index()
    {
        $todolist = $this->loadModel('BusinessStatuses')->find()
            ->where([
                'done'                     => 0,
                'next_contact_time <'      => (new Date("+2 hours"))->i18nFormat('yyyy-MM-dd HH:mm:ss'),
            ])
            ->contain(['Customers' => function ($q) {
                return $q->contain(['CustomerMobiles' => function ($q) {
                    return $q->contain(['CountryCodes']);
                }]);
            },'Users'])
            ->select([
                'id'                => 'BusinessStatuses.id',
                'name'              => 'Customers.name',
                'next_contact_time' => 'BusinessStatuses.next_contact_time',
                'next_note'         => 'BusinessStatuses.next_note',
                'status'            => 'BusinessStatuses.status',
                'userid' => 'Users.userid'
            ])
            ->enableAutoFields(true)
            ->map(function ($row) {
                $row->next_contact_time = (new Time($row->next_contact_time))->i18nFormat('MM-dd HH:mm:ss');
                $mobile                 = $row->customer->customer_mobiles[0];
                $row->mobile            = '+' . $mobile->country_code->country_code . '-' . $mobile->mobile;
                return $row;
            })
            ->toArray();
        if (count($todolist)) {
            $news = [];
            foreach ($todolist as $todo) {
                $news[] = [
                    "title"       => $todo['name'].'|'.$todo['next_contact_time'],
                    "description" => $todo['next_note'],
                    "url"         => 'https://'.$_SERVER['HTTP_HOST'].'/customers/view/'.$todo['customer_id'].'?business_status_id='.$todo['id'].'&done=1',
                ];
            }
            $this->CorpWechat->sendNews($news,['touser' => $todo['userid']]);
        }
    }
}
