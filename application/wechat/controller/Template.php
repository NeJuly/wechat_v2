<?php
/**
 * Created by Maiya.
 * User: Junely
 * Date: 2018/7/17
 * Time: 11:44
 */

namespace app\wechat\controller;


use app\common\controller\Base;
use app\wechat\model\TemplateMessage;
use think\App;

class Template extends Base
{
    public $tempMsgModel;
    public function __construct(App $app = null)
    {
        $this->tempMsgModel = new TemplateMessage();
        parent::__construct($app);
    }

    /**
     * 获取模板消息列表
     * @throws \Exception
     */
    public function get_template_list()
    {
        $resultData = $this->tempMsgModel->get_all_private_template($this->access_token);
        halt($resultData);
    }


    /**
     * 发送模板消息
     * @throws \Exception
     */
    public function send()
    {
        $resultData = $this->tempMsgModel->send($this->access_token,'o9CL70h99H1AoZR4VyOMNZHmYVcg');
        halt($resultData);
    }
}