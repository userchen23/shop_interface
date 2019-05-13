<?php
namespace app\admin\model;

use app\admin\model\Base;
use app\admin\model\Tag;

/**
 * 
 */
class Goods extends Base
{
    public $table = "goods";

    public function changelist($thelists){
        $tagobj = new Tag;
        $taglists = $tagobj->getlists();
        $taglists = get_key_value($taglists,"id");
        foreach ($taglists as $key => $value) {
            unset($taglists[$key]['id']);
        }
        foreach($thelists as $key => $value) {
            $tagid = trim($value['tag']);
            $tagid = explode(',', $tagid);
            $tmptag  =[];
            foreach ($tagid as $k => $v) {
                $tmptag[]=$taglists[$v];
            }
            $thelists[$key]['tag'] = $tmptag;
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
        $tagid = trim($info['tag']);
        $tagid = explode(',', $tagid);
        $tmptag  =[];
        foreach ($tagid as $k => $v) {
            $tmptag[]=$taglists[$v];
        }
        $info['tag'] = $tmptag;
        return $info;        
    }
    public function formatGood($info){
        $result = [];
        
        $tmp=[
            'id'=>$info['id'],
            'img'=>"http://www.shop.com/".$info['img'],
            'title'=>$info['name'],
            'desc'=>$info['pro'],
            'price'=>$info['price'],
            'tag'=>$info['tag'],
            'content'=>$info['content'],
        ];
        $result[]=$tmp;
        return $result;
    }
    public function formatGoods($lists,$type=0){
        $result = [];

        foreach ($lists as $key => $value) {
            $tmp=[
                'id'=>$value['id'],
                'img'=>"http://www.shop.com/".$value['img'],
                'title'=>$value['name'],
                'desc'=>$value['pro'],
                'price'=>$value['price'],
                'tag'=>$value['tag'],
            ];
            $result[]=$tmp;
        }
        
        return $result;
    }

}