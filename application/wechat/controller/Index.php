<?php
namespace app\wechat\controller;

use app\common\controller\Base;
use app\wechat\model\Account;
use app\wechat\model\Qrcode;
use app\wechat\model\ResposeEvent;
use app\wechat\model\ServiceMessage;
use app\wechat\model\TemplateMessage;
use app\wechat\model\Users;
//use think\Config;
use think\Exception;
use think\facade\Config;

class Index extends Base
{
    public $validate_token = 'weixin';
    /**
     * @throws Exception
     */
    public function index()
    {
        $this->valid();
//        $this->responseMsg();

    }
    /**
     * @throws Exception
     */
    public function valid()
    {
        $echoStr = input("echostr");

        //valid signature , option
        //判定
        if($this->checkSignature()  && $echoStr){
            echo $echoStr;
            exit;
        }else{
            $this->responseMsg();
        }
    }
    /**
     * @return bool
     * @throws Exception
     */
    private function checkSignature()
    {
        // you must define TOKEN by yourself
//        if (!defined("TOKEN")) {
//            throw new Exception('TOKEN is not defined!');
//        }

        $signature = input("signature");
        $timestamp = input("timestamp");
        $nonce = input("nonce");

        $token = $this->validate_token;
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule  形成数组按照字典序排序
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );   //通过sha1加密

        if( $tmpStr == $signature ){
            return true;
        }else{
            return false;
        }
    }

    public function responseMsg()
    {
        //获取微信推送过来的post数据（xml格式）
        $postStr = isset($GLOBALS['HTTP_RAW_POST_DATA']) ? $GLOBALS['HTTP_RAW_POST_DATA'] : file_get_contents("php://input");
        //1.接受普通消息以及被动回复消息
        $this->getUserMessage($postStr);
        //2.消息事件推送
        $this->getEvent($postStr);

    }

    /**
     * 接受普通消息以及被动回复消息
     * @param $postStr
     */
    public function getUserMessage($postStr)
    {
        if (!empty($postStr)) {
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr);
            $msgType = $postObj->MsgType;
            switch ($msgType) {
                case 'text':
                    $contentStr = "您随便输入了一段话";
                    $respose_event_model = new ResposeEvent();
                    $result = $respose_event_model->responseText($postObj,$contentStr);
                    echo $result;
                    break;
                case 'image':
                    $respose_event_model = new ResposeEvent();
                    $result = $respose_event_model->responseNews($postObj,4);
                    echo $result;
                    break;
                case 'voice':
                    $contentStr = "您输入的是语音！";
                    $respose_event_model = new ResposeEvent();
                    $result = $respose_event_model->responseText($postObj,$contentStr);
                    echo $result;
                    break;
                default:
                    echo "Input something...";
                    break;
            }
        }else {
            echo "";
            exit;
        }
    }

    /**
     * 事件消息推送
     * @param $postStr
     * @throws \Exception
     */
    public function getEvent($postStr)
    {

        //判断该数据包是否是订阅的事件推送
        libxml_disable_entity_loader(true);
        $postObj = simplexml_load_string($postStr);

        $accountModel = new Account();
        $appid = 'wx53845bcfe8b420e1';
        /**
         * 获取用户基本信息，存入数据库
         */
        $resultData = $accountModel->get_user_info($this->access_token,$postObj->FromUserName,$appid);
        $userModel = new Users([],$appid);
        $nickname = $userModel->get_nickname($postObj->FromUserName);
        /**
         * 1.event 类型
         */
        if($postObj->MsgType == 'event') {
            //如果是关注 subscribe 事件
            if (strtolower($postObj->Event) == 'subscribe') {
                /**
                 * 回复用户消息(纯文本格式)
                 */
                $content = "{$nickname},欢迎关注Junely的微信公众号";
                $respose_event_model = new ResposeEvent();
                $result = $respose_event_model->responseText($postObj,$content);
                echo $result;
                /**
                 * 用户关注自动回复文本消息
                 */
                $service_message_model = new ServiceMessage();
                $service_message_model->serviceData($postObj,$this->access_token);
                /**
                 * 用户关注自动回复图文消息
                 */
                $service_message_model->serviceData($postObj,$this->access_token,'news');
                /**
                 * 用户关注，发送模板消息
                 */
                if ($resultData){
                    $templateModel = new TemplateMessage();
                    $templateModel->send($this->access_token,$postObj->FromUserName);
                }

            }
            /**
             * 2. click 类型
             */
            if (strtolower($postObj->Event) == 'click'){
                $respose_event_model = new ResposeEvent();
                switch ($postObj->EventKey){
                    case 'TODAY_MUSIC':
                        /**点击菜单拉取消息时的事件推送*/
                        $contentStr = "今天火爆音乐1---{$nickname}";
                        $result = $respose_event_model->responseText($postObj,$contentStr);
                        break;
                    case 'GOOD':
                        /**点击菜单拉取消息时的事件推送*/
                        $contentStr = "别点赞---{$nickname}";
                        $result = $respose_event_model->responseText($postObj,$contentStr);
                        break;
                    case 'show_picture':
                        /**点击菜单拉取消息时的事件推送*/
                        $contentStr = "展示图片---{$nickname}";
                        $result = $respose_event_model->responseText($postObj,$contentStr);
                        break;
                    default:
                        break;
                }
                echo $result;
            }
            if (strtolower($postObj->Event == 'unsubscribe')){
                //这里进行数据库的操作。如果用户取消关注，对项目中数据库的内容进行调整
            }

        }
    }

    /**
     * @throws \Exception
     */
    public function test()
    {
       /* $accountModel = new Account();
        $appid = 'wx53845bcfe8b420e1';
        $resultData = $accountModel->get_user_info($this->access_token,'o9CL70l9qiC5dJj_8Nv4Shqt3Nlw',$appid);
        $userModel = new Users([],$appid);
        $user_count = $userModel->where([
            'appid' => $appid,
            'openid' => $resultData['openid']
        ])->count();

        if ( $user_count > 0 ){
            return true;
        }else{
            $data = [
                'appid' => $appid,
                'openid' => $resultData['openid'],
                'nickname' => $resultData['nickname'],
                'avatar_url' => $resultData['headimgurl'],
                'gender' => $resultData['sex'],
                'city' => $resultData['city'],
                'province' => $resultData['province'],
                'country' => $resultData['country'],
                'language' => $resultData['language'],
                'last_login_ip' => $this->request->server('REMOTE_ADDR') ,
                'remark' => $resultData['remark'],
            ];
            $userModel->save($data);
            return true;
        }*/
        /**
         * 生成二维码
         */
        $qrcodeModel = new Qrcode();
        $result = $qrcodeModel->set_qrcode($this->access_token);
        halt($result);
    }
}
