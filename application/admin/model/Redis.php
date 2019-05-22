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
        $result = [];
        $value=Cache::get($key);
        if ($value) {
            $result=unserialize($value);
        }else{
            $result=null;
        }
        
        return $result;
    }

    public function setToken($info){
        $name = $info['name'];
        $id   = $info['id'];
        $token = md5("crj".md5($name.$id));
        return $token;
    }
    public function getToken($data){
        $result = [];
        $name = $info['name'];
        $id   = $info['id'];
        $token = self::setToken($data);
        $data  =[
            'name'=>$data['name'],
            'id'=>$data['id'],
        ];
        $result = $redis->hmset($token,$data);
        return $token;
    }
    public function TokenClear($token){
        if (Cache::get($token)) {
            Cache::clear($token,NULL);
        }
    }
    // public function checkToken(){
    //     $redis = new \Redis(); 
    //     $redis->connect('127.0.0.1', '6379');

    // }

}