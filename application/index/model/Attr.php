<?php
namespace app\index\model;

use app\index\model\Base;

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
}