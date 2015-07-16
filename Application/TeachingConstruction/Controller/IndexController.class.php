<?php
/*教学建设模块
 * author:xulinjie
 * email:164408119@qq.com
 * create date:2015.07.16
 */
namespace TeachingConstruction\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController {
    public function indexAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教学建设的添加
    public function addAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
}