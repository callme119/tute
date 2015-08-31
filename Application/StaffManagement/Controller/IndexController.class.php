<?php
/*教工管理模块
 * author:weijingyun
 * email:1028193951@qq.com
 * create date:2015.07.16
 */
namespace StaffManagement\Controller;
use Admin\Controller\AdminController;
use StaffManagement\Model\StaffManagementModel;
class IndexController extends AdminController {

    //教工列表显示
    public function indexAction(){
        //获取部门信息
        $url=array(
            "add"=>U('add'),
            "edit"=>U('edit'),
            );
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('url',$url);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate); 
    }
    //教工管理添加教工
    public function addAction(){
        //获取部门列表信息(考虑是否加到初始化方法中，因为编辑也需要)
        //获取岗位列表信息(考虑是否加到初始化方法中，因为编辑也需要)
        //获取角色列表信息(考虑是否加到初始化方法中，因为编辑也需要)
        //传值，前台进行处理
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教工管理编辑
    public function editAction(){
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);  
    }
    //添加教工完成
    public function saveOkAction(){
        $staffModel = new StaffManagementModel;
        $state = $staffModel -> addStaff();
        if($state){
            $this->success('新增成功', 'index');
        }

    }
    //编辑教工完成
    public function updateOkAction(){

    }
}