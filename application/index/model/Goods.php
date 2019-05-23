<?php
namespace app\index\model;

use app\index\model\Base;
use app\index\model\Tag;
use app\index\model\Attr as AttrModel;
use app\index\model\Cart as CartModel;
/**
 * 
 */
class Goods extends Base
{
    public $table = "goods";

    public function changelist($thelists){
        $tag_obj = new Tag;
        $tag_lists = $tag_obj->getlists();
        $tag_lists = get_key_value($tag_lists,"id");
        foreach ($tag_lists as $key => $value) {
            unset($tag_lists[$key]['id']);
        }
        foreach($thelists as $key => $value) {
            $tag_id = explode(',', $value['tag']);
            $tmp_tag  =[];
            foreach ($tag_id as $k => $v) {
                $tmp_tag[]=$tag_lists[$v];
            }
            $thelists[$key]['tag'] = $tmp_tag;
        }
        return $thelists;
    }

    public function changeInfo($info){
        $tag_obj = new Tag;
        $tag_lists = $tag_obj->getlists();
        $tag_lists = get_key_value($tag_lists,"id");
        foreach ($tag_lists as $key => $value) {
            unset($tag_lists[$key]['id']);
        }
        $tag_id = explode(',', $info['tag']);
        $tmp_tag  =[];
        foreach ($tag_id as $k => $v) {
            $tmp_tag[]=$tag_lists[$v];
        }
        $info['tag'] = $tmp_tag;
        return $info;        
    }
    public function formatGood($info){
        
        $tmp=[
            'id'=>$info['id'],
            'img'=>"http://shop.com/".$info['img'],
            'title'=>$info['name'],
            'desc'=>$info['pro'],
            'price'=>$info['price']/100,
            'tag'=>$info['tag'],
            'attr'=>$info['attr'],
            'content'=>htmlspecialchars_decode($info['content']),
        ];
        return $tmp;
    }



    public function formatGoods($lists,$type=0){
        $result = [];
        foreach ($lists as $key => $value) {
            $tmp=[
                'id'=>$value['id'],
                'img'=>"http://shop.com/".$value['img'].'?v=1',
                'title'=>$value['name'],
                'desc'=>$value['pro'],
                'price'=>$value['price']/100,
                'tag'=>$value['tag'],
            ];
            $result[] = $tmp;
        }
        return $result;
    }
    public function getGoodsLists($user_id){
                ///todo
        ///
        $data = [];
        $cart_obj = new CartModel;
        $user_cart = $cart_obj->selectInfo('user_id',$user_id);
        $cart = [];
        $attr_obj = new AttrModel;

        foreach ($user_cart as $key => $value) {
            $goods_info = self::getInfo('id',$value['goods_id']);
            $goods_name = $goods_info['name'];
            $attr_info  = $attr_obj-> getInfo('id',$value['attr_id']);
            $cart[]=[
                'name' => $goods_name,
                'price'=> $goods_info['price']/100,
                'count'=> $value['count'],
                'attr_id'=>$value['attr_id'],
                'attr_type'=>$attr_info['attr_type'],
                'attr_name'=> $attr_info['attr_name'],
                'pic'  => $attr_info['pic'],
            ];
        }
        $data['cart'] =$cart;
        return $data;
    }

}