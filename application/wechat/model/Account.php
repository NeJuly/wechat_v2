<?php
/**
 * Created by Maiya.
 * User: Junely
 * Date: 2018/7/5
 * Time: 10:52
 */

namespace app\wechat\model;


use think\Model;

class Account extends Model
{
    public $autoWriteTimestamp = true;
    public $resultSetType = '\think\Collection';
    protected $type = [
        'create_time' => 'datetime',
        'update_time' => 'datetime',
    ];
    protected $name = 'account';

    /**
     * 通过数据库存储用户的access_token
     * @param $appid
     * @param $appsecret
     * @return array|mixed
     */
    public static function getAccessToken($appid, $appsecret)
    {
        $access_token = self::where([
            ['appid', '=', $appid],
            ['appsecret', '=', $appsecret],
            ['token_expires', '>', time()]
//
        ])
            ->value('access_token');

        if ($access_token) {
            $test_token = file_get_contents('https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token=' . $access_token);
            $test_data = json_decode($test_token, true);
            if (isset($test_data['errcode'])) {
                $accessToken = self::get_wechat_access_token($appid, $appsecret);
            } else {
                $accessToken = $access_token;
            }
        } else {
            $accessToken = self::get_wechat_access_token($appid, $appsecret);
        }
        return $accessToken;
    }

    /**
     * @param $appid
     * @param $appsecret
     * @return array
     * @throws \Exception
     */
    private static function get_wechat_access_token($appid, $appsecret)
    {
        //https请求方式: GET
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid={$appid}&secret={$appsecret}";
        $access_data = https_request($url);
        if (isset($access_data['access_token'])) {
            $access_token = $access_data['access_token'];

            self::where([
                'appid' => $appid,
                'appsecret' => $appsecret
            ])->update([
                'access_token' => $access_token,
                'token_expires' => time() + 7000,
            ]);
        } else {
//            {"errcode":40013,"errmsg":"invalid appid"}
            $access_token = [
                'errcode' => $access_data['errcode'],
                'errmsg' => $access_data['errmsg']
            ];
        }
        return $access_token;
    }

    /**
     * 获取用户基本信息（包括UnionID机制）
     * 开发者可通过OpenID来获取用户基本信息。请使用https协议。
     * @throws \Exception
     */
    public function get_user_info($access_token,$openid,$appid)
    {
        /*接口调用请求说明
        http请求方式: GET
        https://api.weixin.qq.com/cgi-bin/user/info?access_token=ACCESS_TOKEN&openid=OPENID&lang=zh_CN*/
        $resultData = https_request(set_api("/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN"));
        $userModel = new Users([],$appid);
        $userModel->save_user_info($resultData,$appid);
        return true;
    }


}