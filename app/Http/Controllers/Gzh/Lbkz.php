<?php
/**
<<<<<<< Updated upstream
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
                default:
                    $this->other($fromUser,$toUser,$content);
            }

        }
    }

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

//    https://api.weixin.qq.com/cgi-bin/menu/create?access_token=ACCESS_TOKEN

    public function createMenu()
    {
        $access_token = $this->getAccessToken();

        $menuInfo = [
            'button' => [
                array("type" => "click","name" => "按钮1", "key" => "测试2"),
                array(
                    'name' => '菜单2',
                    'sub_button' => array(
                        array('type'=>'view','name'=>'哑巴湖大水怪','url'=>'https://wp.wqzbxh.site'),
                        array('type'=>'click','name'=>'赞一下我','key'=>'V1001_GOOD')
                    )
                )

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
=======
 * Created by : VsCode
 * User: Dumb Lake Monster (Wang Haiyang)
 * Date:  2023/5/3
 * Time:  13:55
 */

namespace App\Http\Controllers;

class Lbkz
{
    public function index()
    {

>>>>>>> Stashed changes
    }
}
