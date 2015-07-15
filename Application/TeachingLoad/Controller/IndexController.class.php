<?php
/*教学工作量模块
 * author:xulinjie
 * email:164408119@qq.com
 * create date:2015.07.15
 */
namespace TeachingLoad\Controller;
use Think\Controller\AdminController;
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