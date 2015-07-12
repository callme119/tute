<?php
/*
 * 本模块实现我的工作管理
 * author：邓浩洋
 * 275111108@qq.com
 * 
 */

namespace Myjob\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController{
    
    public function indexAction() {
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
        $this->assign('YZBODY',$this->fetch('index'));
        $this->display(YZTemplate);
    }
     
    public function  taskDetailAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('taskdetail'));
        $this->display(YZTemplate);
    }
    
     public function  projectDetailAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('projectdetail'));
        $this->display(YZTemplate);
    }
    
    public function  newexamineAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('newexamine'));
        $this->display(YZTemplate);
    }
    
    public function  examinelistAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('examinelist'));
        $this->display(YZTemplate);
    }
    
    public function  doingAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('doing'));
        $this->display(YZTemplate);
    }
    
    public function  finishedAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('finished'));
        $this->display(YZTemplate);
    }
    
    public function  unfinishedAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('unfinished'));
        $this->display(YZTemplate);
    }
    
     public function  finishedprojectdetailAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('finishedprojectdetail'));
        $this->display(YZTemplate);
    }
    
    public function  finishedtaskdetailAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('finishedtaskdetail'));
        $this->display(YZTemplate);
    }
    
     public function  doingtaskdetailAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('doingtaskdetail'));
        $this->display(YZTemplate);
    }
    
    public function  doingprojectdetailAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('doingprojectdetail'));
        $this->display(YZTemplate);
    }
}