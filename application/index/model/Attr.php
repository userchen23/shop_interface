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
                'attrid'=>$value['id'],
                'attrtype'=>$value['attr_type'],
                'attrname'=>$value['attr_name'],
                'pic'=>$value['pic'],
            ];
        }
        return $result;
    }
}