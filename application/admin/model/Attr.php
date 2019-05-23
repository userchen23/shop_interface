<?php
namespace app\admin\model;

use app\admin\model\Base;

/**
 * 
 */
class Attr extends Base
{
    public $table = "attr";

    public function formatAttr($Attr){
        $result = [];
        foreach ($Attr as $key => $value) {
            $result[] =[
                'attr_id'=>$value['id'],
                'attr_type'=>$value['attr_type'],
                'attr_name'=>$value['attr_name'],
                'pic'=>$value['pic'],
            ];
        }
        return $result;
    }

    public function makeTree($attrlists){
        $attr = [];
        $tmp   = [];
        foreach($attrlists as $key =>$value){
            $tmp['name'] = $value['attr_name'];
            $tmp['id']   = $value['id'];
            if(isset($attr[$value['attr_type']])){
                $attr[$value['attr_type']][] = $tmp;
            }else{
                $attr[$value['attr_type']] = [];
                $attr[$value['attr_type']][] = $tmp;
            }
        }
        $tree = [];
        foreach($attr as $key=>$value){
            $tree[]=[
                'type' =>$key,
                'child'=>$value,
            ];
        }
        return $tree;
    }

}
