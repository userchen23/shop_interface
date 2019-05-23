<?php
namespace app\admin\model;

use app\admin\model\Base;
use app\admin\model\User as UserModel;
/**
 * 
 */
class Token extends Base
{
    public $table = "token";

    public function getToken($data){
        $value = serialize($data);
        $token =self::setToken($data);
        $time = time()+86400*30;

        $end = [
            'token'=>$token,
            'value'=>$value,
            'time'=>$time,
        ];
        $result = $this->insert($end);
        if ($result) {
            return $token;
        }else{
            return 0;
        }
    }

    public function setToken($data=[]){
        $time =  md5(time().rand(1,100000));
        $user = md5(serialize($data));
        return 'user_token_'.md5($time. $user);
    }

    public function tokenChecked($token){

        $info = self::getinfo('token',$token);

        if(!$info){
            $result = false;
            return $result;die();
        }
        $now_time =time();
        if ($now_time > $info['time']) {
            $result = false;
            return $result;die();
        }
        $result = $info;
        $result= unserialize($info['value']);
        $user_obj=new UserModel;
        $result= $user_obj->getinfo('id',$result['id']);
        return $result;
    }

    public function tokenClear($token){
        $result = self::doDelete('token',$token);
        return $result;
    }
}