<?php
namespace app\index\model;

use app\index\model\Base;
use think\Db;
/**
 * 
 */
class User extends Base
{
  public $table ='user';

  public function findByPhone($phone){//
      
    $info=$this->where('phone',$phone)->find();
    return $info;
      
  }

  public function setToken(){
    //todo 存token
    
  }

  public function register($data){
    $result = $this->insert($data);
    return $result;
  }
}
    
