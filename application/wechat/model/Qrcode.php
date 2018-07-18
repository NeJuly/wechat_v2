<?php
/**
 * Created by Maiya.
 * User: Junely
 * Date: 2018/7/18
 * Time: 16:11
 */

namespace app\wechat\model;


use think\Model;

class Qrcode extends Model
{
    /**
     * 生成带参数的二维码
     * 临时二维码请求说明

    http请求方式: POST
    URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
    POST数据格式：json
    POST数据例子：{"expire_seconds": 604800, "action_name": "QR_SCENE", "action_info": {"scene": {"scene_id": 123}}}

    或者也可以使用以下POST数据创建字符串形式的二维码参数：
    {"expire_seconds": 604800, "action_name": "QR_STR_SCENE", "action_info": {"scene": {"scene_str": "test"}}}
    永久二维码请求说明

    http请求方式: POST
    URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
    POST数据格式：json
    POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}

    或者也可以使用以下POST数据创建字符串形式的二维码参数：
    {"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "test"}}}
     */
    public function set_qrcode($access_token)
    {
        /*http请求方式: POST
        URL: https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=TOKEN
        POST数据格式：json
        POST数据例子：{"action_name": "QR_LIMIT_SCENE", "action_info": {"scene": {"scene_id": 123}}}
         * */
        $post = [
                "expire_seconds" => 604800,
                "action_name" => "QR_SCENE",
                "action_info" =>[
                "scene" => [
                    "scene_id" => 123
                    ]
                ]
        ]

        ;
        $resultData = https_request(set_api("/qrcode/create?access_token={$access_token}"),$post,true);

        /**
         * {"ticket":"gQH47joAAAAAAAAAASxodHRwOi8vd2VpeGluLnFxLmNvbS9xL2taZ2Z3TVRtNzJXV1Brb3ZhYmJJAAIEZ23sUwMEmm
        3sUw==","expire_seconds":60,"url":"http://weixin.qq.com/q/kZgfwMTm72WWPkovabbI"}
         */
        $result = https_request(set_api("/showqrcode?ticket=".urlencode($resultData['ticket'])));

        return $result;
    }
}