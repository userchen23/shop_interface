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
        $tagobj = new Tag;
        $taglists = $tagobj->getlists();
        $taglists = get_key_value($taglists,"id");
        foreach ($taglists as $key => $value) {
            unset($taglists[$key]['id']);
        }
        foreach($thelists as $key => $value) {
            $tagid = explode(',', $value['tag']);
            $tmptag  =[];
            foreach ($tagid as $k => $v) {
                $tmptag[]=$taglists[$v];
            }
            $thelists[$key]['tag'] = $tmptag;
        }
        //读取attr
        $attrobj = new AttrModel;
        $attrlists = $attrobj->getlists();
        $attrlists = get_key_value($attrlists,"id");
        foreach($thelists as $key => $value) {
            $attrid = explode(',', $value['attrid']);
            $tmptag  =[];
            foreach ($attrid as $k => $v) {
                $tmpattr[]=$attrlists[$v];
            }
            $thelists[$key]['attr'] = $tmpattr;
        }
        return $thelists;
    }

    public function changeInfo($info){
        $tagobj = new Tag;
        $taglists = $tagobj->getlists();
        $taglists = get_key_value($taglists,"id");
        foreach ($taglists as $key => $value) {
            unset($taglists[$key]['id']);
        }
        $tagid = explode(',', $info['tag']);
        $tmptag  =[];
        foreach ($tagid as $k => $v) {
            $tmptag[]=$taglists[$v];
        }
        $info['tag'] = $tmptag;
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