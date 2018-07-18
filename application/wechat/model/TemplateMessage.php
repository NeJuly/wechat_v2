<?php
/**
 * Created by Maiya.
 * User: Junely
 * Date: 2018/7/17
 * Time: 11:38
 */

namespace app\wechat\model;


use think\Model;

class TemplateMessage extends Model
{
    /**
     * 获得模板ID
    从行业模板库选择模板到帐号后台，获得模板ID的过程可在微信公众平台后台完成。为方便第三方开发者，提供通过接口调用的方式来获取模板ID，具体如下：
     */


    /**
     * 获取模板列表
     * @param $access_token
     * @return array|mixed
     * @throws \Exception
     */
    public function get_all_private_template($access_token)
    {
       /* http请求方式：GET
        https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token=ACCESS_TOKEN*/
       $resultData = https_request(WX_PREFIX."/template/get_all_private_template?access_token={$access_token}");
       return $resultData;
    }

    /**
     * 发送模板消息
     * @param $access_token
     * @return mixed|string
     * @throws \Exception
     */
    public function send($access_token,$openid)
    {
        /*http请求方式: POST
        https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=ACCESS_TOKEN*/
//        POST数据示例如下：

        $data = [
           "touser"=>"{$openid}",
           "template_id"=>"WWcyysb8AA2GAxPTnaZh7c_BQ2dw0eDsRUOEUFA_3pE",
           "url"=>"http://www.baidu.com",           
           "data"=>[
                   "first"=> [
                       "value"=>"好好学习！",
                       "color"=>"#173177"
                   ],
                   "keyword1"=>[
                       "value"=>"天天向上！",
                       "color"=>"#173177"
                   ],
                   "keyword2"=> [
                       "value"=>"健健康康！",
                       "color"=>"#173177"
                   ],
                   "remark"=>[
                       "value"=>"恭喜发财，发个红包吧！",
                       "color"=>"#173177"
                   ]
           ]
       ];
        $resultData = https_request(set_api("/message/template/send?access_token={$access_token}"),$data,true);
        return $resultData;
    }
}