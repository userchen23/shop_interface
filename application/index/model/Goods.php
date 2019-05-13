<?php
namespace app\index\model;

use app\index\model\Base;
use app\index\model\Tag;

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
            $tagid = explode(',', $value['tag']);
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
                'price'=>$value['price'],
                'tag'=>$value['tag'],
            ];
            $result[] = $tmp;
        }
        return $result;
    }

}