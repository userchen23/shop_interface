<?php
namespace app\admin\model;

use \amdin\Db;
use \think\Model;
use \think\Collection;
use \app\admin\model\Redis;
use \think\File;
/**
 * 
 */
class Base extends Model
{   

    public function add($data){
        $result = $this->insert($data);

        return $result;
    }

    public function addGetId($data){
        $result = $this->insertGetId($data);
        return $result;
    }
    public function updateField($field1,$info,$field2,$data){
        $result=$this->where($field1,$info)->setField($field2, $data);
        return $result;
    }

    public function getInfo($field,$value){
        $info = $this->where($field,$value)->find();
        if ($info) {
            $info = $info->toArray();
        }
        return $info;
    }
    public function selectInfo($field,$value){
        $info =$this->where($field,$value)->select();
        if ($info) {
            $info = lists_to_array($info);
        }
        return $info;
    }

    public function getLists(){
        $table_Lists="crj_".$this->table."_Lists";
        $redis_obj= new Redis;
        $value=$redis_obj->getRedis($table_Lists);
        if (empty($value)) {
            $lists = $this->select();
            if ($lists) {
                $result =lists_to_array($lists);
            }else{
                $result = 0;
            }
            $redis_obj->setRedis($table_Lists,$result);
        }else{
            $result=$value;
        }
        
        return $result;
    }

    public function changeLists(){
        $result=self::getLists();
        $umsg=get_key_value($result,'id');
        return $umsg;
      }
    
    public function doupdate($id,$data){
        $result = $this->where('id', $id)->update($data);
        return $result;
    }

    public function dodelete($field,$value){
        $result = $this->where($field,$value)->delete();
        return $result;
    }
}
