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
        if (request()->isPost()) {
            $token = input('post.token');
            if (empty($token)) {
                $error = 2;
                $msg = 'tonken为空';
            }else{
                $tokenobj = new UserToken;
                $result = $tokenobj->tokenChecked($token);
                if (!$result) {
                    $error = 3;
                    $msg = 'token不存在或过期';
                }
            }   
        }else{
            $error=1;
            $msg = '未接受到POST请求';
        }

        $result = response($error, $msg, $data);
        return json_encode($result);
    }

    public function info(){
        $error = 0;
        $msg = '成功';
        $data = [];

        $token = input('token');

        if (empty($token)) {
            $error = 1;
            $msg = 'tonken为空';
            $result = response($error, $msg, $data);
            return json_encode($result);die();
        }

        $tokenobj = new UserToken;
        $result = $tokenobj->tokenChecked($token);

        if (!$result) {
            $error = 2;
            $msg = 'token不存在或过期';
            $result = response($error,$msg,$data);
            return json_encode($result);die();
        }

        $info = [
            "id"=>!empty($result['id'])?$result['id']:'',
            "username"=>!empty($result['username'])?$result['username']:'',
            "phone"=>!empty($result['phone'])?$result['phone']:'',
        ];
        $data = [
            "info"=>$info,
        ];

        $result = response($error, $msg, $data);
        return json_encode($result);die();
    }

    public function doLogin(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if(request()->isPost()){
            $data = input('post.');
            $obj = new Usermodel;
            $info = $obj->findByPhone($data['phone']);

            if(empty($info)) {
                $error = 2;
                $msg = "用户不存在";
            }else{
                if ($info['password'] == $data['password']) {
                    $data = [
                        "name"=>$info['username'],
                        "id"=>$info['id'],
                    ];
                    $tokenobj= new UserToken;
                    $token =$tokenobj-> getToken($data);
                    if ($token === 0) {
                        $error=4;
                        $msg = "token存入失败";
                    }
                    $data = [];
                    $data['token'] = $token;
                }else{
                    $error = 3;
                    $msg = "密码错误";
                }           
            }          
        }else{
                $error = 1;
                $msg = "未接受到POST请求";
        }

        $result=response($error,$msg,$data);
        return json_encode($result);
    }

    public function loginout(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (request()->isPost()) {
            $token = input('post.token');
            if (empty($token)) {
                $error = 2;
                $msg = 'tonken为空';
            }else{
                $tokenobj = new UserToken;
                $result = $tokenobj->tokenClear($token);
                if ($result===0) {
                    $error = 3;
                    $msg = '失败';
                }
            }  
        }else{
            $error=1;
            $msg = '未接受到POST请求';
        }
        $result = response($error,$msg,$data);
        return json_encode($result);
    }

    public function reg(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (request()->isPost()) {
            $postdata = input('post.');
            if (empty($postdata)) {
                $error = 2;
                $msg = 'data为空';
            }else{
                if (empty($postdata['username'])||empty($postdata['phone'])||empty($postdata['password'])) {
                    $error = 3;
                    $msg = "用户名、电话、密码不能为空";
                }else{
                    $postdata2 = [
                        "username" =>$postdata['username'],
                        "phone"=>$postdata['phone'],
                        "password"=>$postdata['password'],
                    ];
                    $userobj = new Usermodel;
                    $info = $userobj->findByPhone($postdata2['phone']);
                    if(!empty($info)) {
                        $error = 4;
                        $msg = "电话已被注册";
                    }else{
                        $result =$userobj-> register($postdata2);
                        $msg ="注册成功";
                    }
                }
            }  
        }else{
            $error=1;
            $msg = '未接受到POST请求';
        }
        $result = response($error,$msg,$data);
        return json_encode($result);
    }
}