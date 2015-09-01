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
        //获取教工列表
        $staffModel = new StaffManagementModel;
        $staffList = $staffModel -> getStaffList();
        $staffList = $this -> _addurl($staffList,'_url');
        //var_dump($staffList);
        $this->assign('staffList',$staffList);
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate); 
    }
    //教工管理添加教工
    public function addAction(){
        //获取部门列表信息(考虑是否加到初始化方法中，因为编辑也需要)
        //获取岗位列表信息(考虑是否加到初始化方法中，因为编辑也需要)
        //获取角色列表信息(考虑是否加到初始化方法中，因为编辑也需要)
        //传值，前台进行处理
        $url = U('save');
        $this->assign('url',$url);
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教工管理编辑
    public function editAction(){
        $id = I('get.id');
        //获取当前教工的信息
        $staffModel = new StaffManagementModel;
        $staffInfo = $staffModel -> getStaffById($id);

        //传值
        $url = U('update?id='.$id);
        $this->assign('url',$url);
        $this ->assign('staffInfo',$staffInfo);
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);  
    }
    //删除教工
    public function deleteAction(){
        $id = I('get.id');
        $staffModel = new StaffManagementModel;
        $state = $staffModel -> deleteStaff($id);
        if($state){
            $this->success('删除成功', U('index'));
        }
    }
    //添加教工完成
    public function saveAction(){
        $staffModel = new StaffManagementModel;
        $state = $staffModel -> addStaff();

        if($state){
            $this->success('新增成功', 'index');
        }
    }
    //编辑教工完成
    public function updateAction(){
        $staffModel = new StaffManagementModel;
        $state = $staffModel -> updateStaff();
        if($state){
            $this->success('修改成功', U('index'));
        }
    }

    /**
     * 添加url信息
     * @param  [type] 要添加的数组
     * @param  [type] 要填写的下标名
     * @return [type] 拼接好url信息的数组
     */
    private function _addurl($array,$string){
        $data = $array;
        foreach ($data as $key => $value) {
            $data[$key][$string] = array(
                'edit'=>U('edit?id='.$value['id']),
                'delete'=>U('delete?id='.$value['id']),
                );
        }
        return $data;
    }
}