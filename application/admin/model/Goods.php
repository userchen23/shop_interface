<?php
namespace app\admin\model;

use app\admin\model\Base;
use app\admin\model\Tag;
use app\admin\model\Attr as AttrModel;
/**
 * 
 */
class Goods extends Base
{
    public $table = "goods";

    public function changelist($thelists){
        //读取tag
        $tag_obj = new Tag;
        $tag_lists = $tag_obj->getlists();
        $tag_lists = get_key_value($tag_lists,"id");
        foreach ($tag_lists as $key => $value) {
            unset($tag_lists[$key]['id']);
        }
        foreach($thelists as $key => $value) {
            $tag_id = explode(',', $value['tag']);
            $tam_tag  =[];
            foreach ($tag_id as $k => $v) {
                $tam_tag[]=$tag_lists[$v];
            }
            $thelists[$key]['tag'] = $tam_tag;
        }
        //读取attr
        $attr_obj = new AttrModel;
        $attr_lists = $attr_obj->getlists();
        $attr_lists = get_key_value($attr_lists,"id");
        foreach($thelists as $key => $value) {
            $attr_id = explode(',', $value['attr_id']);
            $tam_tag  =[];
            foreach ($attr_id as $k => $v) {
                $tmp_tag[]=$attr_lists[$v];
            }
            $thelists[$key]['attr'] = $tmp_tag;
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
        $tam_tag  =[];
        foreach ($tag_id as $k => $v) {
            $tam_tag[]=$tag_lists[$v];
        }
        $info['tag'] = $tam_tag;
        return $info;        
    }
    public function formatGood($info){
        
        $tmp=[
            'id'=>$info['id'],
            'img'=>"http://www.shop.com/".$info['img'],
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
                'img'=>"http://www.shop.com/".$value['img'],
                'title'=>$value['name'],
                'desc'=>$value['pro'],
                'price'=>$value['price']/100,
                'tag'=>$value['tag'],
                'attr'=>$value['attr'],
            ];
            $result[] = $tmp;
        }
        return $result;
    }

}