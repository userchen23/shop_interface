<?php
namespace app\index\controller;

use \think\Controller;
use app\index\model\Goods as GoodsModel;
use app\index\model\Banner;
use app\index\model\Token as TokenModel;
use app\index\model\Attr  as AttrModel;
use app\index\model\Cart  as CartModel;
/**
 * 
 */
class Goods extends Controller
{

    public function index(){
        $error = 0;
        $msg = '成功';
        $data = [];

        $goodsobj = new GoodsModel;
        $goodslists=$goodsobj->getLists();
        $endgoods =$goodsobj->changelist($goodslists);
        $endgoods = $goodsobj->formatGoods($endgoods);
        $data['goods']=$endgoods;

        $bannerobj = new Banner;
        $bannerlist = $bannerobj->getLists();
        $endbanner = $bannerobj->formatBanner($bannerlist);
        $data['banner']=$endbanner;
        echoJson($error,$msg,$data);
    }

    public function detail($id){
        $data = [];
        $error = 0;
        $msg = '成功';
        if (empty($id)) {
            $error = 1;
            $msg = "未传递参数ID";
            echoJson($error,$msg,$data);           
        }
        $goodobj = new GoodsModel;
        $goodinfo =$goodobj->getinfo('id',$id);
        $goodinfo =$goodobj->changeinfo($goodinfo);
        //从goods表获取attrid 
        $attrstr = $goodinfo['attrid'];
        //转换成数组
        if ($attrstr) {
            $attrarr = explode(',', $attrstr);
        }else{
            $attrarr = [];
        }
        $attrlists = [];
        $attrobj = new AttrModel;
        foreach ($attrarr as $key => $value) {
            if (!empty($value)) {
            $attrlists[] = $attrobj->getInfo('id',$value); 
            }
        }
        if ($attrlists) {
            $endattr = $attrobj->formatAttr($attrlists);
        }else{
            $endattr = [];
        }
        $goodinfo['attr'] = $endattr;
        if (empty($goodinfo)) {
            $error = 2;
            $msg = "未查找到相关物品";
        }else{
            $endgoods = $goodobj->formatGood($goodinfo);
            $data['info']=$endgoods;
        }
        echoJson($error,$msg,$data);
    }


    public function cart(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }
        $postdata = input('post.');
        if (empty($postdata)) {
            $error = 2;
            $msg = 'data为空';
            echoJson($error,$msg,$data);
        }
        if (empty($postdata['goods_id'])||empty($postdata['token'])||empty($postdata['count'])) {
            $error = 3;
            $msg = "缺少必要参数";
            echoJson($error,$msg,$data);
        }

        //获取userid
        $token = $postdata['token'];
        $tokenobj = new TokenModel;
        $result = $tokenobj->tokenChecked($token);
        if (!$result) {
            $error = 4;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $userid = $result['id'];
        //获取goodsid
        $goodsid = $postdata['goods_id'];
        $goodobj = new GoodsModel;
        $info = $goodobj->getInfo('id',$goodsid);
        if (!$info) {
            $error = 5;
            $msg = "未找到商品";
            echoJson($error,$msg,$data);
        }
        //获取price
        $price = $info['price']/100;
        //获取count
        $count = $postdata['count'];
        //获取color,size,attrid
        $color = !empty($postdata['color'])?$postdata['color']:'';
        $size  = !empty($postdata['size'])?$postdata['size']:'';
        $attrid= !empty($postdata['attrid'])?$postdata['attrid']:'';
        //判断是否重复
        $cartobj = new CartModel;
        $cartlists = $cartobj->selectInfo('userid',$userid);
        $result = false;
        if(!empty($cartlists)){
            foreach ($cartlists as $key => $value) {
                if ($value['goodsid']==$goodsid && $value['attrid']==$attrid) {
                    $count = $value['count'] +$count;
                    $result = $cartobj->updateField('id',$value['id'],'count',$count);
                    if (!$result) {
                        $error = 7;
                        $msg ='增加失败';
                        echoJson($error,$msg,$data);
                    }
                    $data =$goodobj-> getGoodsLists($userid);
                    echoJson($error,$msg,$data);
                }
            }
        }
        $endcart=[
            'goodsid'=> $goodsid,
            'userid' => $userid,
            'price'  => $price*100,
            'count'  => $count,
            'color'  => $color,
            'size'   => $size,
            'attrid' => $attrid,
        ];
        $cartobj = new CartModel;
        $result = $cartobj->add($endcart);
        if (!$result) {
            $error = 6;
            $msg ='添加失败';
            echoJson($error,$msg,$data);
        }
        $result = $cartobj->getLists();

        $data = $goodobj-> getGoodsLists($userid);
        echoJson($error,$msg,$data);
    }

    public function cartInfo(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }
        $token =input('post.token');
        if (empty($token)) {
            $error = 2;
            $msg   = 'token为空';
            echoJson($error,$msg,$data);
        }
        //判断token,获取用户id
        $tokenobj = new TokenModel;
        $result   = $tokenobj->tokenChecked($token);
        if (!$result) {
            $error = 4;
            $msg   = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $userid = $result['id'];
//获取列表，返回数据
        $goodobj = new GoodsModel;
        $data = $goodobj-> getGoodsLists($userid);
        echoJson($error,$msg,$data);
    }
    
    public function cartDec(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }

        $postdata = input('post.');

        if (empty($postdata['goods_id'])||empty($postdata['token'])) {
            $error = 2;
            $msg = "缺少必要参数";
            echoJson($error,$msg,$data);
        }

        $token =$postdata['token'];
        //判断token
        $tokenobj = new TokenModel;
        $result = $tokenobj->tokenChecked($token);
        if (!$result) {
            $error = 3;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $userid = $result['id']; 
        //post中物品id,属性id,count
        $goodsid = $postdata['goods_id'];
        $attrid  = !empty($postdata['attrid'])?$postdata['attrid']:'';
        $count   = !empty($postdata['count']) ?$postdata['count']:1;
        $cartobj = new CartModel;
        $usercart = $cartobj->selectInfo('userid',$userid);
        foreach ($usercart as $key => $value) {
            if ($value['goodsid'] ==$goodsid && $value['attrid']==$attrid) {
                $tmpcount = 0;
                $tmpcount = $value['count'] - $count;
                if ($tmpcount===0) {
                    $result = $cartobj->dodelete('id',$value['id']);
                }else{
                    if ($tmpcount>0) {
                        $result = $cartobj->updateField('id',$value['id'],'count',$tmpcount);
                    }else{
                        $error = 4;
                        $msg = '删除数量大于原有数量';
                        echoJson($error,$msg,$data);
                    }
                }
                if (!$result) {
                    $error = 5;
                    $msg ='删除失败';
                    echoJson($error,$msg,$data); 
                }

                $data =$goodobj-> getGoodsLists($userid);
                echoJson($error,$msg,$data); 
            }
        }
        $error = 6;
        $msg = '参数有误';
        echoJson($error,$msg,$data); 
    }

    public function cartClear(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }

        $postdata = input('post.');

        if (empty($postdata['token'])) {
            $error = 2;
            $msg = "token为空";
            echoJson($error,$msg,$data);
        }
        $token =$postdata['token'];
        //判断token
        $tokenobj = new TokenModel;
        $result = $tokenobj->tokenChecked($token);
        if (!$result) {
            $error = 3;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $userid = $result['id'];

        $cartobj = new CartModel;
        $usercart = $cartobj->selectInfo('userid',$userid);
        if (empty($usercart)) {
            $error = 4;
            $msg ='用户没有物品';
            echoJson($error,$msg,$data); 
        }
        foreach ($usercart as $key => $value) {

            $result = $cartobj->dodelete('id',$value['id']);
            if (!$result) {
                $error = 5;
                $msg ='删除失败';
                echoJson($error,$msg,$data); 
            }
            echoJson($error,$msg,$data); 
        }
        $error = 6;
        $msg = '参数有误';
        echoJson($error,$msg,$data);
    }
    public function cartDetele(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }

        $postdata = input('post.');

        if (empty($postdata['goods_id'])||empty($postdata['token'])) {
            $error = 2;
            $msg = "缺少必要参数";
            echoJson($error,$msg,$data);
        }

        $token =$postdata['token'];
        //判断token
        $tokenobj = new TokenModel;
        $result = $tokenobj->tokenChecked($token);
        if (!$result) {
            $error = 3;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $userid = $result['id']; 
        //post中物品id,属性id,count
        $goodsid = $postdata['goods_id'];
        $attrid  = !empty($postdata['attrid'])?$postdata['attrid']:'';
        $cartobj = new CartModel;
        $usercart = $cartobj->selectInfo('userid',$userid);
        foreach ($usercart as $key => $value) {
            if ($value['goodsid'] ==$goodsid && $value['attrid']==$attrid) {
                $result = $cartobj->dodelete('id',$value['id']);
                    if (!$result){
                        $error = 4;
                        $msg = '删除失败';
                        echoJson($error,$msg,$data);
                    }
                echoJson($error,$msg,$data); 
            }
        }
        $error = 5;
        $msg = '参数有误';
        echoJson($error,$msg,$data); 
    }
}