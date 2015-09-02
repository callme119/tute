<?php
/*
 * 
 */

namespace Examine\Controller;
use Admin\Controller\AdminController;
use Post\Model\PostModel;
use Examine\Model\ExamineModel;
class IndexController extends AdminController{
     /**
      * 通过post模块取所有岗位信息传给用户点选
      * 无返回值
      */
     public function  newExamineAction(){
        //调用post模块中的getPostInfo方法
        //传给V层
        $post = new PostModel;
        $data = $post->select();
        $this->assign("post",$data);
        $this->assign('YZBODY',$this->fetch('newexamine'));
        $this->display(YZTemplate);
    }

    //初始化审批列表
    public function  indexAction(){
        $model = new ExamineModel;
        $data = $model->index();
        $this->assign('examine',$data);
        $this->assign('YZBODY',$this->fetch('examinelist'));
        $this->display(YZTemplate);
    }
    
    //添加审批流程
    public function saveAction() {
        //先将post过来的岗位信息存审批对应的链表信息
        $post = I('post.chain');
        $model = new ExamineModel;
        //获取该审批对应的头结点id
        $id = $model->saveChain($post);
        $name = I('post.name');
        $model->save($id,$name);
    }
    
}
    