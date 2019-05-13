<?php
namespace app\admin\model;

use \think\Db;
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

    public function getInfo($id){
        $info = $this->where('id',$id)->find();
        $info = $info->toArray();
        return $info;
    }

    public function getLists(){
        $table_Lists="crj_".$this->table."_Lists";
        $redis_obj= new Redis;
        $value=$redis_obj->getRedis($table_Lists);
        if (empty($value)) {
            $lists = $this->select();
            $result =lists_to_array($lists);
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

    public function dodelete($id){
        $result = $this->where('id',$id)->delete();
        return $result;
    }
}
