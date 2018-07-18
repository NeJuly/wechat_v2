<?php
/**
 * Created by Maiya.
 * User: Junely
 * Date: 2018/7/17
 * Time: 17:48
 */

namespace app\wechat\model;


use think\facade\Config;
use think\facade\Request;
use think\Model;

class Users extends Model
{
    protected $table;
    protected $autoWriteTimestamp = 'datetime';
    public $updateTime = false;
    public function __construct($data = [],$table)
    {
        $this->connection = Config::get('database.users');
        $this->table = $table;
        parent::__construct($data);

    }
    public function save_user_info($resultData,$appid)
    {
        $user_count = $this->where([
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
                'last_login_ip' => Request::server('REMOTE_ADDR') ,
                'remark' => $resultData['remark'],
            ];
            $this->save($data);
            return true;
        }
    }
    public function get_nickname($openid)
    {
        $nickname = $this->where('openid',$openid)->value('nickname');
        return $nickname;
    }
}