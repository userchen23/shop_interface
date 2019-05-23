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

        $goods_obj = new GoodsModel;
        $goods_lists=$goods_obj->getLists();
        $end_goods =$goods_obj->changelist($goods_lists);
        $end_goods = $goods_obj->formatGoods($end_goods);
        $data['goods']=$end_goods;

        $banner_obj = new Banner;
        $banner_lists = $banner_obj->getLists();
        $end_banner = $banner_obj->formatBanner($banner_lists);
        $data['banner']=$end_banner;
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
        $goods_obj = new GoodsModel;
        $goods_info =$goods_obj->getinfo('id',$id);
        $goods_info =$goods_obj->changeinfo($goods_info);
        //从goods表获取attr_id 
        $attrstr = $goods_info['attr_id'];
        //转换成数组
        if ($attrstr) {
            $attrarr = explode(',', $attrstr);
        }else{
            $attrarr = [];
        }
        $attr_lists = [];
        $attr_obj = new AttrModel;
        foreach ($attrarr as $key => $value) {
            if (!empty($value)) {
            $attr_lists[] = $attr_obj->getInfo('id',$value); 
            }
        }
        if ($attr_lists) {
            $end_attr = $attr_obj->formatAttr($attr_lists);
        }else{
            $end_attr = [];
        }
        $goods_info['attr'] = $end_attr;
        if (empty($goods_info)) {
            $error = 2;
            $msg = "未查找到相关物品";
        }else{
            $end_goods = $goods_obj->formatGood($goods_info);
            $data['info']=$end_goods;
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
        $post_data = input('post.');
        if (empty($post_data)) {
            $error = 2;
            $msg = 'data为空';
            echoJson($error,$msg,$data);
        }
        if (empty($post_data['goods_id'])||empty($post_data['token'])||empty($post_data['count'])) {
            $error = 3;
            $msg = "缺少必要参数";
            echoJson($error,$msg,$data);
        }

        //获取user_id
        $token = $post_data['token'];
        $token_obj = new TokenModel;
        $result = $token_obj->tokenChecked($token);
        if (!$result) {
            $error = 4;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $user_id = $result['id'];
        //获取goods_id
        $goods_id = $post_data['goods_id'];
        $goods_obj = new GoodsModel;
        $info = $goods_obj->getInfo('id',$goods_id);
        if (!$info) {
            $error = 5;
            $msg = "未找到商品";
            echoJson($error,$msg,$data);
        }
        //获取price
        $price = $info['price']/100;
        //获取count
        $count = $post_data['count'];
        //获取color,size,attr_id
        $color = !empty($post_data['color'])?$post_data['color']:'';
        $size  = !empty($post_data['size'])?$post_data['size']:'';
        $attr_id= !empty($post_data['attr_id'])?$post_data['attr_id']:'';
        //判断是否重复
        $cart_obj = new CartModel;
        $cart_lists = $cart_obj->selectInfo('user_id',$user_id);
        $result = false;
        if(!empty($cart_lists)){
            foreach ($cart_lists as $key => $value) {
                if ($value['goods_id']==$goods_id && $value['attr_id']==$attr_id) {
                    $count = $value['count'] +$count;
                    $result = $cart_obj->updateField('id',$value['id'],'count',$count);
                    if (!$result) {
                        $error = 7;
                        $msg ='增加失败';
                        echoJson($error,$msg,$data);
                    }
                    $data =$goods_obj-> getGoodsLists($user_id);
                    echoJson($error,$msg,$data);
                }
            }
        }
        $endcart=[
            'goods_id'=> $goods_id,
            'user_id' => $user_id,
            'price'  => $price*100,
            'count'  => $count,
            'color'  => $color,
            'size'   => $size,
            'attr_id' => $attr_id,
        ];
        $cart_obj = new CartModel;
        $result = $cart_obj->add($endcart);
        if (!$result) {
            $error = 6;
            $msg ='添加失败';
            echoJson($error,$msg,$data);
        }
        $result = $cart_obj->getLists();

        $data = $goods_obj-> getGoodsLists($user_id);
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
        $token_obj = new TokenModel;
        $result   = $token_obj->tokenChecked($token);
        if (!$result) {
            $error = 4;
            $msg   = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $user_id = $result['id'];
//获取列表，返回数据
        $goods_obj = new GoodsModel;
        $data = $goods_obj-> getGoodsLists($user_id);
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

        $post_data = input('post.');

        if (empty($post_data['goods_id'])||empty($post_data['token'])) {
            $error = 2;
            $msg = "缺少必要参数";
            echoJson($error,$msg,$data);
        }

        $token =$post_data['token'];
        //判断token
        $token_obj = new TokenModel;
        $result = $token_obj->tokenChecked($token);
        if (!$result) {
            $error = 3;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $user_id = $result['id']; 
        //post中物品id,属性id,count
        $goods_id = $post_data['goods_id'];
        $attr_id  = !empty($post_data['attr_id'])?$post_data['attr_id']:'';
        $count   = !empty($post_data['count']) ?$post_data['count']:1;
        $cart_obj = new CartModel;
        $user_cart = $cart_obj->selectInfo('user_id',$user_id);
        foreach ($user_cart as $key => $value) {
            if ($value['goods_id'] !=$goods_id || $value['attr_id']!=$attr_id) {
                $error = 6;
                $msg = '参数有误';
                echoJson($error,$msg,$data);
            }
            $tmpcount = 0;
            $tmpcount = $value['count'] - $count;
            if ($tmpcount===0) {
                $result = $cart_obj->dodelete('id',$value['id']);
            }else{
                if ($tmpcount<0) {
                    $error = 4;
                    $msg = '删除数量大于原有数量';
                    echoJson($error,$msg,$data);
                }
                $result = $cart_obj->updateField('id',$value['id'],'count',$tmpcount);
            }
            if (!$result) {
                $error = 5;
                $msg ='删除失败';
                echoJson($error,$msg,$data); 
            }
            $goods_obj = new GoodsModel;
            $data =$goods_obj-> getGoodsLists($user_id);
            echoJson($error,$msg,$data);
        }
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

        $post_data = input('post.');

        if (empty($post_data['token'])) {
            $error = 2;
            $msg = "token为空";
            echoJson($error,$msg,$data);
        }
        $token =$post_data['token'];
        //判断token
        $token_obj = new TokenModel;
        $result = $token_obj->tokenChecked($token);
        if (!$result) {
            $error = 3;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $user_id = $result['id'];

        $cart_obj = new CartModel;
        $user_cart = $cart_obj->selectInfo('user_id',$user_id);
        if (empty($user_cart)) {
            $error = 4;
            $msg ='用户没有物品';
            echoJson($error,$msg,$data); 
        }
        foreach ($user_cart as $key => $value) {

            $result = $cart_obj->dodelete('id',$value['id']);
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
    public function cartDelete(){
        $error = 0;
        $msg = '成功';
        $data = [];
        if (!request()->isPost()) {
            $error=1;
            $msg = '未接受到POST请求';
            echoJson($error,$msg,$data);
        }

        $post_data = input('post.');

        if (empty($post_data['goods_id'])||empty($post_data['token'])) {
            $error = 2;
            $msg = "缺少必要参数";
            echoJson($error,$msg,$data);
        }

        $token =$post_data['token'];
        //判断token
        $token_obj = new TokenModel;
        $result = $token_obj->tokenChecked($token);
        if (!$result) {
            $error = 3;
            $msg = 'token不存在或过期';
            echoJson($error,$msg,$data);
        }
        $user_id = $result['id']; 
        //post中物品id,属性id,count
        $goods_id = $post_data['goods_id'];
        $attr_id  = !empty($post_data['attr_id'])?$post_data['attr_id']:'';
        $cart_obj = new CartModel;
        $user_cart = $cart_obj->selectInfo('user_id',$user_id);
        foreach ($user_cart as $key => $value) {
            if ($value['goods_id'] ==$goods_id && $value['attr_id']==$attr_id) {
                $result = $cart_obj->dodelete('id',$value['id']);
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