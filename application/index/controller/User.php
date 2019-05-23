<?php
namespace app\index\controller;
use app\index\model\User as Usermodel;
use think\Controller;
use think\Db;
use app\index\model\Redis as Redismodel;
use app\index\model\Token as UserToken;
/**
 * 
 */
class User extends Controller
{
    
    public function loginCheck(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()){
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }
        $token = input('post.token');
        if (empty($token)) {
            $error = 2;
            $msg = 'tonken为空';
            echoJson($error,$msg,$data);
        }
        $token_obj = new UserToken;
        $result = $token_obj->tokenChecked($token);
        if (!$result) {
            $error = 3;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }

        echoJson($error,$msg,$data);
    }

    public function info(){
        $error = 0;
        $msg = '成功';
        $data = [];

        $token = input('token');

        if (empty($token)) {
            $error = 1;
            $msg = 'tonken为空';
            echoJson($error,$msg,$data);
        }

        $token_obj = new UserToken;
        $result = $token_obj->tokenChecked($token);

        if (!$result) {
            $error = 2;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }

        $info = [
            "id"=>!empty($result['id'])?$result['id']:'',
            "user_name"=>!empty($result['user_name'])?$result['user_name']:'',
            "phone"=>!empty($result['phone'])?$result['phone']:'',
        ];
        $data = [
            "info"=>$info,
        ];

        echoJson($error,$msg,$data);
    }

    public function doLogin(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if(!request()->isPost()){
                $error = 1;
                $msg = "未接受到POST请求";
                echoJson($error,$msg,$data);
        }
        $data = input('post.');
        $obj = new Usermodel;
        $info = $obj->findByPhone($data['phone']);
        if(empty($info)) {
            $error = 2;
            $msg = "用户不存在";
            echoJson($error,$msg,$data);
        }
        if ($info['password'] != $data['password']){
            $error = 3;
            $msg = "密码错误";
            echoJson($error,$msg,$data);
        }
        $data = [
            "name"=>$info['user_name'],
            "id"=>$info['id'],
        ];
        $token_obj= new UserToken;
        $token =$token_obj-> getUserToken($data);
        if (!$token) {
            $error=4;
            $msg = "token存入失败";
            echoJson($error,$msg,$data);
        }
        $data = [];
        $data['token'] = $token;              
        echoJson($error,$msg,$data);
    }

    public function loginout(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }
        $token = input('post.token');
        if (empty($token)) {
            $error = 2;
            $msg = 'tonken为空';
            echoJson($error,$msg,$data);
        }
        $token_obj = new UserToken;
        $result = $token_obj->tokenClear($token);
        if (!$result) {
            $error = 3;
            $msg = '失败';
            echoJson($error,$msg,$data);
        }
        echoJson($error,$msg,$data);
    }

    public function reg(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }
        $post_data = input('post.');
        if (empty($post_data)) {
            $error = 2;
            $msg = 'data为空';
            echoJson($error,$msg,$data);
        }
        if (empty($post_data['user_name'])||empty($post_data['phone'])||empty($post_data['password'])) {
            $error = 3;
            $msg = "用户名、电话、密码不能为空";
            echoJson($error,$msg,$data);
        }
        if (!is_numeric($post_data['phone'])||strlen($post_data['phone'])!=11) {
            $error = 5;
            $msg   = "电话不符合规范";
            echoJson($error,$msg,$data);
        }
        $post_data2 = [
            "user_name" =>$post_data['user_name'],
            "phone"=>$post_data['phone'],
            "password"=>$post_data['password'],
        ];
        $user_obj = new Usermodel;
        $info = $user_obj->findByPhone($post_data2['phone']);
        if(!empty($info)) {
            $error = 4;
            $msg = "电话已被注册";
            echoJson($error,$msg,$data);
        }
        $result =$user_obj-> register($post_data2);
        $msg ="注册成功";  
        echoJson($error,$msg,$data);
    }
}