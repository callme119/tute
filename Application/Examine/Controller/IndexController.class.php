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
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
        $this->assign('YZBODY',$this->fetch('newexamine'));
        $this->display(YZTemplate);
    }
    //
    public function saveAction() {
        //保存用户点选的岗位信息
        $model = new ExamineModel;
        $model->save();
    }
    
    //初始化审批列表
    public function  indexAction(){
        $model = new ExamineModel;
        $data = $model->index();
//        $tpl = T("Admin@Admin/index");
//        define('YZTemplate', $tpl);
//        $this->assign('YZBODY',$this->fetch('examinelist'));
//        $this->display(YZTemplate);
    }
    
    //添加审批流程
    public function addAction() {
        $model = new ExamineModel;
        $data = $model->add();
        var_dump($data);
        //将岗位名称传入V层的select中
    }
    
}
    