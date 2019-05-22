<?php
namespace tool;
use think\File;
/**
 * 
 */
class FileHandle
{
    
    public static function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('img');
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'uploads');
            
            if($info){
                $arr=[
                    "type"=>$info->getExtension(),
                    "saveName"=>"uploads/".$info->getSaveName(),
                    "fileName"=>$info->getFilename(),
                ];
                return $arr;
                die();
            }else{
                // 上传失败获取错误信息
                return 0;
            }
        }
        return 0;
    }

    public static function uploadImg($img_name,$img_address){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($img_name);
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS. 'img' . DS . $img_address);
            if($info){
                $arr=[
                    "type"=>$info->getExtension(),
                    "saveName"=>'img'. DS . $img_address . DS .$info->getSaveName(),
                    "fileName"=>$info->getFilename(),
                ];
                return $arr;
                die();
            }else{
                // 上传失败获取错误信息
                return 0;
            }
        }
        return 0;
    }
    
    public static function uploadPic(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('pic');
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->validate(['ext'=>'jpg,png,gif'])->move(ROOT_PATH . 'public' . DS . 'img'.DS.'attr');
            
            if($info){
                $arr=[
                    "type"=>$info->getExtension(),
                    "saveName"=>"http://shop.com/img/attr/".$info->getSaveName(),
                    "fileName"=>$info->getFilename(),
                ];
                return $arr;
                die();
            }else{
                // 上传失败获取错误信息
                return 0;
            }
        }
        return 0;
    }
}