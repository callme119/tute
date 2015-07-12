<?php
/*
 * 所有controller都需要继承该类
 * 在本类中，将进行用户的权限验主，统一设置模板等操作。
 * author:panjie joinpan@gmail.com
 */

namespace Examine\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController{
    
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
     public function  editexamineAction(){
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('editexamine'));
        $this->display(YZTemplate);
    }
}
    