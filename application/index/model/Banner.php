<?php
namespace app\index\model;

use app\index\model\Base;

/**
 * 
 */
class Banner extends Base
{
    public $table = "banner";

    public function formatBanner($banner_list){
        $end_banner=[];
        foreach ($banner_list as $key => $value) {
            $tmp=[
                'img'=>$value['img'],
                'url'=>$value['url'],
            ];
            $end_banner[] = $tmp;
        }
        return $end_banner;
    }
}