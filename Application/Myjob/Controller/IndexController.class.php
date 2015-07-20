<?php
/*
 * 我的工作初始化模块
 * creat by 邓浩洋 2015年7月15日14:44:26
 * 2751111108@qq.com
 */

namespace Myjob\Controller;
use Admin\Controller\AdminController;
use Myjob\Model\JobModel;
class IndexController extends AdminController{
    /**
     * 我的工作中待办工作界面
     * 初始化界面
     * 无参数，无返回值
     * 2015年7月16日09:36:08
     */
    
    public function indexAction() {
        //取用户id
        $id = I('get.userid');
        
        //调用model中的初始化方法
        $jobModel = new JobModel();
        $jobModel->setUserId(1);
        $data = $jobModel->getUnfinishedInfoByUserId();
        //var_dump($data);
//        $tpl = T("Admin@Admin/index");
//        define('YZTemplate', $tpl);
//        $this->assign('YZBODY',$this->fetch('index'));
//        $this->display(YZTemplate);
        
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