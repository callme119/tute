<?php
/*
 * 所有controller都需要继承该类
 * 在本类中，将进行用户的权限验主，统一设置模板等操作。
 * author:panjie joinpan@gmail.com
 */

namespace Pattern\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController{
    
    public function indexAction() {
        $tpl = T("Admin@Admin/index");
       define('YZTemplate', $tpl);
       $this->assign('YZBODY',$this->fetch('index'));
      $this->display(YZTemplate);
          
        
    }
     
    public function index2Action() {
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
        $this->assign('YZBODY',$this->fetch('index2'));
        $this->display(YZTemplate);
    }
    
    public function editAction() {
        $tpl = T("Admin@Admin/index");
        define('YZTemplate', $tpl);
        $this->assign('YZBODY',$this->fetch('edit'));
        $this->display(YZTemplate);
    }
    public function selectAction() {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate); 
    }
    public function successAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    
}