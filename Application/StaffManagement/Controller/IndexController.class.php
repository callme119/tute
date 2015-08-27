<?php
/*教工管理模块
 * author:weijingyun
 * email:1028193951@qq.com
 * create date:2015.07.16
 */
namespace StaffManagement\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController {
    public function indexAction(){
        $url=array(
            "add"=>U('add'),
            "edit"=>U('edit'),
            );
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('url',$url);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate); 
    }
    //教工管理编辑
    public function editAction(){
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教工管理添加教工
    public function addAction(){
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
}