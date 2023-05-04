<?php
/**
 * Created by : PhpStorm
 * User: 哑巴湖大水怪（王海洋）
 * Date: 2023/5/3
 * Time: 20:58
 */

namespace App\Http\Controllers\Gzh;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;

class Lbkz
{

    private $appid = 'wxdb300ca6e6d3464e';
    private $AppSecret = '4898092950075f870188a21e34d7cfe7';
    public function checkSignature( Request $request ) {

        $input = $request->all();
        if($request->method()=='GET'){
            # 一定要抓取4个参数
            $echoStr  = $input[ "echostr" ];
            $signature = $input[ "signature" ];
            $timestamp = $input[ "timestamp" ];
            $nonce   = $input[ "nonce" ];
            # 微信官方验证方式
            $token ='dictionary'; #填写微信公众平台输入的token
            $tmpArr = [ $token, $timestamp, $nonce ];
            sort( $tmpArr, SORT_STRING );
            $tmpStr = implode( $tmpArr );
            $tmpStr = sha1( $tmpStr );
            if( $tmpStr == $signature ){
                // 处理接收到的消息
                return response($echoStr);
            } else{
                return response();
            }
        }else{
            $postData = file_get_contents("php://input");
            $xmlData = simplexml_load_string($postData);
            file_put_contents('robots.txt',$postData);
            $fromUser = $xmlData->FromUserName;
            $toUser = $xmlData->ToUserName;
            $msgType = $xmlData->MsgType;
            $content = $xmlData->Content;
            switch ($msgType)
            {
                case "text":
                    $this->hanldText($fromUser,$toUser,$content);
                case "event":
                    $event = $xmlData->Event;
                    $eventKey = $xmlData->EventKey;
                    if($event == 'CLICK'){
                        $this->hanldClick($eventKey,$fromUser,$toUser,$content);
                    }

                    if($event == 'scancode_push'){
                        $this->hanldScancodePush($eventKey,$fromUser,$toUser,$content,$xmlData);
                    }
                default:
                    $this->other($fromUser,$toUser,$content);
            }

        }
    }

    /***
     * @return mixed
     * 测试
     */
    public function getAccessToken()
    {

        Redis::select(6);
        if(Redis::get('lbkz_access_token') != null )
        {
            $access_token = Redis::get('lbkz_access_token');
            return $access_token;
        }else{
            $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$this->appid.'&secret='.$this->AppSecret;
            $response =  Http::get($url);
            $responseResult = $response->getBody()->getContents();
            $result = json_decode($responseResult, true);
            Redis::set('lbkz_access_token', $result['access_token'], $result['expires_in']);
            return  $result['access_token'];
        }
    }

    /***
     * @return void
     * @throws \GuzzleHttp\Exception\GuzzleException
     * 生成菜单
     */

//    https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN

    public function createMenu()
    {
        $access_token = $this->getAccessToken();

        $menuInfo = [
            'button' => [
                array("type" => "click","name" => "你好", "key" => "V1002"),
                array(
                    'name' => '个人信息',
                    'sub_button' => array(
                        array('type'=>'view','name'=>'个人介绍','url'=>'https://wp.wqzbxh.site/?page_id=2'),
                        array('type'=>'scancode_push','name'=>'扫一扫','key'=>'V1003'),
                        array('type'=>'location_select','name'=>'获取地理位置','key'=>'V1004'),
                    )
                ),
                array(
                    'name' => '博文查阅',
                    'sub_button' => array(
                        array('type'=>'view','name'=>'Msql数据库','url'=>'https://wp.wqzbxh.site/?cat=9'),
                        array('type'=>'view','name'=>'PHP在线编辑','url'=>'https://wp.wqzbxh.site/?p=87'),
                    )
                ),

            ]
        ];
        $client = new Client([
            'base_uri' => 'https://api.weixin.qq.com/cgi-bin/menu/',
        ]);
        try {
            $response = $client->request('POST', 'create', [
                'query' => [
                    'access_token' => $access_token,
                ],
                'body' => json_encode($menuInfo,JSON_UNESCAPED_UNICODE),//含有中文。不能让其乱码
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if ($responseBody['errcode'] === 0) {
                echo 'Custom menu created successfully!';
            } else {
                echo 'Failed to create custom menu: ' . $responseBody['errmsg'];
            }
        } catch (RequestException $e) {
            echo 'Failed to send request: ' . $e->getMessage();
        }
    }

    public function hanldText($fromUser,$toUser,$content)
    {
        $response = "<xml><ToUserName><![CDATA[" . $fromUser . "]]></ToUserName><FromUserName><![CDATA[" . $toUser . "]]></FromUserName><CreateTime>" . time() . "</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[" . $content . "]]></Content></xml>";
        echo $response;
    }

    public function other($fromUser,$toUser,$content)
    {
        $response = "<xml><ToUserName><![CDATA[" . $fromUser . "]]></ToUserName><FromUserName><![CDATA[" . $toUser . "]]></FromUserName><CreateTime>" . time() . "</CreateTime><MsgType><![CDATA[text]]></MsgType><Content><![CDATA[暂时未定义此类消息]]></Content></xml>";
        echo $response;
    }

    public function hanldClick($eventKey,$fromUser,$toUser,$content)
    {
        if($eventKey == "V1002" ){
            $response = "<xml>
                  <ToUserName><![CDATA[". $fromUser ."]]></ToUserName>
                  <FromUserName><![CDATA[". $toUser ."]]></FromUserName>
                  <CreateTime>" . time() . "</CreateTime>
                  <MsgType><![CDATA[image]]></MsgType>
                  <Image>
                    <MediaId><![CDATA[0YKgg8iew5mJajuBQIRKR2nQE8lLm4LBwTdKXiGPtaBujDY1MbXQ_LXlCGIXNixv]]></MediaId>
                  </Image>
                  </xml>";
                }
        echo $response;
    }

    public function hanldScancodePush($eventKey,$fromUser,$toUser,$content,$xmldata)
    {
            $ScanResult = '123';
            $response = "<xml>
                            <ToUserName><![CDATA[" . $fromUser . "]]></ToUserName>
                            <FromUserName><![CDATA[" . $toUser . "]]></FromUserName>
                            <CreateTime>" . time() . "</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA[调用了扫一扫,扫描结果信息：".$ScanResult."]]></Content>
                            </xml>";
//        }
        echo $response;
    }


    /***
     * 上传素材
     */
    public function upFile(){
        $access_token = $this->getAccessToken();
        $type = 'image'; // 上传的素材类型，可选值包括 image、voice、video、thumb
        $filepath = public_path('images/http.png'); // 上传的文件路径，本地文件或远程 URL 都可
        $client = new Client([
            'base_uri' => 'https://api.weixin.qq.com/cgi-bin/media/upload',
        ]);
        $data = [
            'name'     => 'media',
            'contents' => fopen($filepath, 'r'),
        ];
        try {
            $response = $client->request('POST', 'upload', [
                'query' => [
                    'access_token' => $access_token,
                    'type' => $type
                ],
                'multipart' => [
                    [
                        'name' => 'media',
                        'contents' => fopen($filepath, 'r'), // 替换成实际的图片文件路径
                    ],
                ],
            ]);

            $responseBody = json_decode($response->getBody(), true);

            if (isset($responseBody['media_id'])) {
                echo 'Temporary media uploaded successfully! Media ID: ' . $responseBody['media_id'];
            } else {
                echo 'Failed to upload temporary media: ' . $responseBody['errmsg'];
            }
        } catch (RequestException $e) {
            echo 'Failed to send request: ' . $e->getMessage();
        }
    }

    /**
     * 添加客服接口
     */
    public function addKfAccess(){
        $access_token = $this->getAccessToken();

        $client = new Client([
            'base_uri' => 'https://api.weixin.qq.com/customservice/',
        ]);

        $kf_account = 'test1@test';
        $nickname = '客服1';

        $url = 'kfaccount/add?access_token=' . $access_token;

        try {
            $response = $client->request('POST', $url, [
                'json' => [
                    'kf_account' => $kf_account,
                    'nickname' => $nickname,
                ],
            ]);


            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody['errcode'] === 0) {
                echo 'Custom service added successfully!';
            } else {
                echo 'Failed to add custom service: ' . $responseBody['errmsg'];
            }
        } catch (RequestException $e) {
            echo 'Failed to send request: ' . $e->getMessage();
        }
    }


}
