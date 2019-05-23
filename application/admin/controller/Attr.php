<?php
namespace app\admin\controller;

use \think\Controller;
use app\admin\model\Attr as AttrModel;
/**
 * 
 */
class Attr extends Controller
{   
    public function attrLists(){
        $attr_obj   = new AttrModel;
        $attr_lists = $attr_obj->getLists();
        $this->assign('attr_lists',$attr_lists);
        return $this->fetch('attrLists');
    }
    // public function add(){
    //     return $this->fetch('add');
    // }
    // public function save(){
    //     $data=input('post.');
    //     if (!$data) {
    //         $this->error('未获取到数据');
    //     }
    //     $time = time();
    //     //$content = htmlspecialchars($data['content'], ENT_QUOTES);
    //     $attr_name =$data['attr_name']?$data['attr_name']:'';
        
    //     $end=[
    //         'attr_name'        => $attr_name,
    //         'create_time'=> $time,
    //         'update_time'=> $time,
    //     ];
    //     if ($img=\tool\FileHandle::uploadImg('pic','attr')) {
    //         $end['pic']='http://shop.com/'.$img['saveName'];
    //     }

    //     $attr_obj = new attrModel;
    //     $result     = $attr_obj->add($end);

    //     if ($result) {
    //         $this->success('添加成功','attrLists');
    //         die();
    //     }else{
    //         $this->error('添加失败','attrLists');
    //         die();
    //     }
    // }
    public function delete(){
        if (empty($_GET['id'])) {
            $this->error('无id传入');
        }
        $id         = $_GET['id'];
        $attr_obj = new AttrModel;
        $result     = $attr_obj->dodelete('id',$id);
        if ($result) {
            $this->success('删除成功');
            die();
        }else{
            $this->error('删除失败');
            die();
        }
    }
    public function update(){
        if (empty($_GET['id'])) {
            $this->error('无id传入');
        }
        $id         = $_GET['id'];
        $attr_obj = new AttrModel;
        $result     = $attr_obj->getInfo('id',$id);
        $attr_name        = !empty($result['attr_name'])?$result['attr_name']:'';
        $this->assign('id',$id);
        $this->assign('attr_name',$attr_name);
        return $this->fetch('update');die();
    }

    public function doUpdate(){
        $data=input('post.');
        if (empty($data)) {
            $this->error('未获取到数据');
        }
        //$content = htmlspecialchars($data['content'], ENT_QUOTES);
        $attr_name = !empty($data['attr_name'])?$data['attr_name']:'';
        $id  = !empty($data['id'])?$data['id']:'';
        $end=[
            'attr_name'        => $attr_name,
        ];
        if ($img=\tool\FileHandle::uploadImg('pic','attr')) {
            $end['pic']= 'http://shop.com/'. $img['saveName'];
        }

        $attr_obj = new AttrModel;
        $result     = $attr_obj->doupdate($id,$end);

        if ($result) {
            $this->success('修改成功','attrLists');
            die();
        }else{
            $this->error('修改失败','attrLists');
            die();
        }
    }
    
}