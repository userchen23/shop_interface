<?php
namespace app\admin\controller;

use \think\Controller;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Banner as BannerModel;
use app\admin\model\Tag;
use app\admin\model\User as UserModel;
/**
 * 
 */
class User extends Controller
{   
    public function userLists(){
        $user_obj   = new UserModel;
        $user_lists = $user_obj->getLists();

        $end_lists = [];
        foreach ($user_lists as $key => $value) {
            $end_lists[] = [
                'id'       =>$value['id'],
                'user_name'=>$value['username'],
                'phone'    =>$value['phone'],
            ];
        }
        $this->assign('user_lists',$end_lists);
        return $this->fetch('userLists');
    }
    public function add(){
        return $this->fetch('add');
    }
    public function save(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $msg = '未接受到POST请求';
            $this->error($msg,'userLists');die();
        }
        $postdata = input('post.');
        if (empty($postdata)) {
            $msg = 'data为空';
            $this->error($msg,'userLists');die();
        }
        if (empty($postdata['user_name'])||empty($postdata['phone'])||empty($postdata['password'])) {
            $msg = "用户名、电话、密码不能为空";
            $this->error($msg,'userLists');die();
        }
        if (!is_numeric($postdata['phone'])||strlen($postdata['phone'])!=11) {
            $msg   = "电话不符合规范";
            $this->error($msg,'userLists');die();
        }
        $postdata2 = [
            "username" =>$postdata['user_name'],
            "phone"    =>$postdata['phone'],
            "password" =>$postdata['password'],
        ];
        $userobj = new Usermodel;
        $info = $userobj->findByPhone($postdata2['phone']);
        if(!empty($info)) {
            $msg = "电话已被注册";
            $this->error($msg,'userLists');die();
        }
        $result =$userobj-> register($postdata2);
        $msg ="注册成功";  
        $this->success($msg,'userLists');die();
    }

    public function reNameUser(){
        $id = $_GET['id'];
        $this->assign('id',$id);
        return $this->fetch('reNameUser');die();
    }

    public function doReName(){
        $id = !empty(input('post.id'))?input('post.id'):'';
        $username = !empty(input('post.user_name'))?input('post.user_name'):'';

        $user_obj  = new UserModel;
        $result    = $user_obj->updateField('id',$id,'username',$username);
        if($result){
            $this->success('成功','userLists');die();
        }
        $this->error('失败','userLists');die();
    }

    public function delete(){
        $id = !empty(input('get.id'))?input('get.id'):'';
        $user_obj = new UserModel;
        $result   = $user_obj->dodelete('id',$id);
        if($result){
            $this->success('成功','userLists');die();
        }
        $this->error('失败','userLists');die();
    }
    
}