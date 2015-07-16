<?php
/*服务育人模块
 * author:xulinjie
 * email:164408119@qq.com
 * create date:2015.07.16
 */
namespace ServiceEducation\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController {
    public function indexAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教学工作的添加
    public function addAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
}