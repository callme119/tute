<?php
/*学科业绩模块
 * author:xulinjie
 * email:164408119@qq.com
 * create date:2015.07.16
 */
namespace AcademicPerformance\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController {
    public function indexAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //研究生教学的添加
    public function graduateAddAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //学科与科研平台的添加
    public function platformAddAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //科研奖励的添加
    public function ScientificAwardAddAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
}