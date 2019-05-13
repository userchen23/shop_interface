<?php
namespace app\admin\model;

use app\admin\model\Base;

/**
 * 
 */
class Banner extends Base
{
    public $table = "banner";

    public function formatBanner($bannerlist){
        $endbanner=[];
        foreach ($bannerlist as $key => $value) {
            $tmp=[
                'img'=>$value['img'],
                'url'=>$value['url'],
            ];
            $endbanner[] = $tmp;
        }
        return $endbanner;
    }
}