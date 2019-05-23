<?php
namespace app\admin\controller;

use \think\Controller;
use app\admin\model\Goods as GoodsModel;
use app\admin\model\Tag as TagModel;
use app\admin\model\Banner;
use app\admin\model\Attr as AttrModel;
/**
 * 
 */
class Goods extends Controller
{
    public function goodsLists(){
        $goods_obj = new GoodsModel;
        $goods_lists=$goods_obj->getLists();
        $this->assign('goods_lists',$goods_lists);
        return $this->fetch('goodslists');
    }
    public function addAttr(){
        $post_data = input('post.');
        if (empty($post_data['attr_arr'])) {
            $attr_str = '';
        }else{
            $attr_str = implode(',', $post_data['attr_arr']);
        }
        $goods_id = $post_data['goods_id'];
        $this->assign('goods_id',$goods_id);
        $this->assign('attr_str',$attr_str);
        return $this->fetch('addAttr');
    }
    public function attrInfo($goods_id){
        $attr_obj = new AttrModel;
        $attr_lists = $attr_obj->selectInfo('goods_id',$goods_id);
        $this->assign('attr_lists',$attr_lists);
        $this->assign('goods_id',$goods_id);
        return $this->fetch('attrInfo');

    }

    public function attrSave(){
        $post_data = input('post.');
        $goods_id   = !empty($post_data['goods_id'])?$post_data['goods_id']:'';
        $attr_type = !empty($post_data['attr_type'])?$post_data['attr_type']:'';
        $attr_name = !empty($post_data['attr_name'])?$post_data['attr_name']:'';
        if ($img=\tool\FileHandle::uploadImg('pic','attr')) {
            $pic= "http://shop.com/" . $img['saveName'];
        }else{
            $pic = '';
        }
        $data = [
            'attr_type'=> $attr_type,
            'attr_name'=> $attr_name,
            'pic'      => $pic,
            'goods_id'  => $goods_id,
        ];
        $attr_obj = new AttrModel;
        $result1  = $attr_obj->addGetId($data);
        if (!$result1) {
            echo "attradd error";die();
        }
        if (!empty($post_data['attr_str'])) {
            $attr_str=$post_data['attr_str'].','.$result1;
        }else{
            $attr_str=$result1;
        }
        $goods_obj= new GoodsModel;
        $result2  = $goods_obj->updateField('id',$goods_id,'attr_id',$attr_str);
        if (!$result2) {
            $this->error();die();
        }
        $this->success('成功','goods/goodslists');die();
    }
    public function add(){
        $tag_obj = new TagModel;
        $tag_lists=$tag_obj->getLists();
        $this->assign('tag_lists',$tag_lists);
        return $this->fetch('add');
    }
    public function save(){
        $data=input('post.');
        if (empty($data)) {
            $this->error('未获取到数据');
        }
        $time = time();
        //$content = htmlspecialchars($data['content'], ENT_QUOTES);
        $name  =!empty($data['name'])?$data['name']:'';
        $pro   =!empty($data['pro'])?$data['pro']:'';
        $price =!empty($data['price'])?$data['price']:'';
        $tag   =!empty($data['tag'])?$data['tag']:'';
        if (isset($data['content'])) {
            $content =!empty($data['content'])?$data['content']:'';
        }else{
            $content = '';
        }
        
        if ($tag) {
            $tag=implode(',', $tag);
        }

        $end=[
            'name'=>$name,
            'pro'=>$pro,
            'price'=>$price*100,
            'tag'=>$tag,
            'content'=>$content,
            'create_time'=>$time,
            'update_time'=>$time,
        ];
        if ($img=\tool\FileHandle::uploadImg('img','goods')) {
            $end['img']="http://shop.com/" . $img['saveName'];
        }

        $goods_obj = new GoodsModel;
        $result  = $goods_obj->add($end);
        
        if ($result) {
            $this->success('添加成功');
            die();
        }else{
            $this->error('添加失败','goodslists');
            die();
        }
    }

    public function delete(){
        if (empty($_GET['id'])) {
            $this->error('无id传入');
        }
        $id         = $_GET['id'];
        $goods_obj = new GoodsModel;
        $result     = $goods_obj->dodelete('id',$id);
        if ($result) {
            $this->success('删除成功','goodsLists');
            die();
        }else{
            $this->error('删除失败','goodsLists');
            die();
        }
    }

    public function update(){
        if (empty($_GET['id'])) {
            $this->error('无id传入');
        }
        $id         = $_GET['id'];
        $goods_obj  = new GoodsModel;
        $result     = $goods_obj->getInfo('id',$id);
        $name       = !empty($result['name'])?$result['name']:'';
        $pro        = !empty($result['pro'])?$result['pro']:'';
        $price      = !empty($result['price'])?$result['price']:'';

        $goods_info = [
            'id'    => $id,
            'name'  => $name,
            'pro'   => $pro,
            'price' => $price/100,
        ];

        $this->assign('goods_info',$goods_info);
        return $this->fetch('update');die();
    }

    public function doUpdate(){
        $time = time();

        $post_data = input('post.');
        $id  = $post_data['id'];
        $end = [];
        $end = [
            'name'  => $post_data['name'],
            'pro'   => $post_data['pro'],
            'price' => $post_data['price']*100,
        ];        
        if (!empty($post_data['content'])) {
            $end['content'] =$post_data['content'];
        }
        if ($img=\tool\FileHandle::uploadImg('img','goods')) {
            $end['img']="http://shop.com/" . $img['saveName'];
        }
        $goods_obj = new GoodsModel;
        $result     = $goods_obj->doupdate($id,$end);

        if ($result) {
            $this->success('修改成功','goodsLists');
            die();
        }else{
            $this->error('修改失败','goodsLists');
            die();
        }
    }
    
}

