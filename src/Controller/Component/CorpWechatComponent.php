<?php
namespace Cake\Controller\Component;
use Cake\Controller\Component;
use Cake\Network\Http\Client;
use Cake\Cache\Cache;

class CorpWechatComponent extends Component
{
    protected $corpid = 'wx3fd9e869d53f67d4';
    protected $agentid = 0;
    protected $secret = '71rEmD4_4DhweATR0CB5Gdp_dvNMIa4lkS8jQrfdKSo';
    
    public function getAccessToken()
    {
        $http        = new Client();
        $jsonPayload = [
            'corpid'     => $this->corpid,
            'corpsecret' => $this->secret,
        ];
        $url      = 'https://qyapi.weixin.qq.com/cgi-bin/gettoken';
        $response = $http->get($url, $jsonPayload, ['type' => 'json'])->json;

        if (isset($response['errcode']) && $response['errcode'] != 0) {
            return false;
        }

        // Cache::write('corp_wechat_access_token',$response,);
        return $response['access_token'];
    }
    //获取部门信息
    public function departmentList($id=1)
    {
        $access_token = $this->getAccessToken();

        if($access_token){
            $http         = new Client();
            $url      = 'https://qyapi.weixin.qq.com/cgi-bin/department/list?access_token='.$access_token;
            if($id) $url .= '&id='.$id;
            $response = $http->get($url, null, ['type' => 'json'])->json;
            return $response;
        }
            
    }
    //获取成员信息
    public function user($userid)
    {
        $access_token = $this->getAccessToken();

        if($access_token){
            $http         = new Client();
            $url      = 'https://qyapi.weixin.qq.com/cgi-bin/user/get?access_token='.$access_token.'&userid='.$userid;
            $response = $http->get($url, null, ['type' => 'json'])->json;
            if (isset($response['errcode']) && $response['errcode'] != 0) {
                return false;
            }
            return $response;
        }            
    }
    //获取成员列表
    public function userList($department_id=1,$fetch_child=false,$status=0)
    {
        $access_token = $this->getAccessToken();

        if($access_token){
            $http         = new Client();
            $url      = 'https://qyapi.weixin.qq.com/cgi-bin/user/list?access_token='.$access_token.'&department_id='.$department_id.'&fetch_child='.$fetch_child.'&status='.$status;
            $response = $http->get($url, null, ['type' => 'json'])->json;
            return $response;
        }            
    }
    //群发消息
    public function sendNews($news,$touserInfo)
    {
        $access_token = $this->getAccessToken();

        if($access_token){
            $http         = new Client();
            $jsonPayload  = $touserInfo+[
                "msgtype" => "news",
                "agentid" => $this->agentid,
                "news"    => [
                    "articles" => $news,
                ],
                "safe"    => 0,
            ];
            $url      = 'https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token='.$access_token;
            $response = json_decode($this->https_request($url, $jsonPayload, 'json'), true);
            return $response;
        }
            
    }
    private function https_request($url, $data, $type) {
        if ($type == 'json') {
            $headers = array("Content-type: application/json;charset=UTF-8", "Accept: application/json", "Cache-Control: no-cache", "Pragma: no-cache");
            $data = json_encode($data);
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}
