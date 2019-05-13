<?php
namespace app\admin\model;

use think\Model;
use think\Cache;
/**
 * 
 */
class Redis extends Model
{

    public function setRedis($key,$value){
        if (Cache::get($key)) {
            Cache::clear($key,NULL);
        }
        $value=serialize($value);
        $result=Cache::set($key,$value,20); 
        return $result;
    }

    public function getRedis($key){
        $value=Cache::get($key);
        if ($value) {
            $result=unserialize($value);
        }else{
            $result=null;
        }
        
        return $result;
    }

    public function set_token($info){
        $name = $info['name'];
        $id   = $info['id'];
        $token = md5("crj".md5($name.$id));
        setcookie("crj_token",$token);
        $result = $redis->hmset($id, array('name'=>$name, 'id'=>$id));

        return $token;
    }
    public function checkToken(){
        $redis = new \Redis(); 
        $redis->connect('127.0.0.1', '6379');

    }
}