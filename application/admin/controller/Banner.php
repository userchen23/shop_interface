<?php
namespace app\admin\controller;

use \think\Controller;
use app\admin\model\Banner as BannerModel;
/**
 * 
 */
class Banner extends Controller
{   
    public function bannerLists(){
        $banner_obj   = new BannerModel;
        $banner_lists = $banner_obj->getLists();
        $this->assign('banner_lists',$banner_lists);
        return $this->fetch('bannerLists');
    }
    public function add(){
        return $this->fetch('add');
    }
    public function save(){
        $data=input('post.');
        if (empty($data)) {
            $this->error('未获取到数据');
        }
        $time = time();
        //$content = htmlspecialchars($data['content'], ENT_QUOTES);
        $url =$data['url']?$data['url']:'';
        
        $end=[
            'url'        => $url,
            'create_time'=> $time,
            'update_time'=> $time,
        ];
        if ($img=\tool\FileHandle::uploadImg('img','banner')) {
            $end['img']='http://shop.com/'.$img['saveName'];
        }

        $banner_obj = new BannerModel;
        $result     = $banner_obj->add($end);

        if ($result) {
            $this->success('添加成功','bannerLists');
            die();
        }else{
            $this->error('添加失败','bannerLists');
            die();
        }
    }
    public function delete(){
        if (empty($_GET['id'])) {
            $this->error('无id传入');
        }
        $id         = $_GET['id'];
        $banner_obj = new BannerModel;
        $result     = $banner_obj->dodelete('id',$id);
        if ($result) {
            $this->success('删除成功','bannerLists');
            die();
        }else{
            $this->error('删除失败','bannerLists');
            die();
        }
    }
    public function update(){
        if (empty($_GET['id'])) {
            $this->error('无id传入');
        }
        $id         = $_GET['id'];
        $banner_obj = new BannerModel;
        $result     = $banner_obj->getInfo('id',$id);
        $url        = !empty($result['url'])?$result['url']:'';
        $this->assign('id',$id);
        $this->assign('url',$url);
        return $this->fetch('update');die();
    }

    public function doUpdate(){
        $data=input('post.');
        if (empty($data)) {
            $this->error('未获取到数据');
        }
        $time = time();
        //$content = htmlspecialchars($data['content'], ENT_QUOTES);
        $url = !empty($data['url'])?$data['url']:'';
        $id  = !empty($data['id'])?$data['id']:'';
        $end=[
            'url'        => $url,
            'update_time'=> $time,
        ];
        if ($img=\tool\FileHandle::uploadImg('img','banner')) {
            $end['img']= 'http://shop.com/'. $img['saveName'];
        }
        $banner_obj = new BannerModel;
        $result     = $banner_obj->doupdate($id,$end);

        if ($result) {
            $this->success('添加成功','bannerLists');
            die();
        }else{
            $this->error('添加失败','bannerLists');
            die();
        }
    }
    
}