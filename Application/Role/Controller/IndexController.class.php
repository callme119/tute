<?php

/*
 *
 * @author xuao
 */
namespace Role\Controller;
use Admin\Controller\AdminController;
use Role\Model\RoleModel;
use Menu\Model\MenuModel;
class IndexController extends AdminController
{
    //初始化方法
    public function indexAction()
    {
        //获取page信息
        $page = I('get.id',1);
        //获取角色列表
        $roleModel = new RoleModel();
        $roleList = $roleModel->getRoleList($page);
        $roleList = $this->_addurl($roleList,"_url");
        //传值
        $this->assign('roleList',$roleList);
        $this->assign('YZBODY',$this->fetch());
        //前台显示
        $this->display(YZTemplate);
    }
    //添加角色
    public function addRoleAction(){
        $this->assign("js",$this->fetch("addRoleJs"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //显示角色人员信息
    public function peopleAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //删除角色
    public function deleteRoleAction(){

    }
    //编辑角色
    public function editRoleAction(){
        //获取该角色具体信息
        $id = I('get.id');
        $roleModel = new RoleModel();
        $roleInfo = $roleModel->getRoleById($id);
        //页面显示
        $this->assign('roleInfo',$roleInfo);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //保存
     public function saveOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    //添加
    public function addOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    //移除用户
    public function movePeopleOkAction(){
        $url = U('people');
        $this->success('保存成功',$url);
    }
    //添加用户
    public function addPeopleOkAction(){
        $url = U('people');
        $this->success('保存成功',$url);
    }
    public function addPeopleAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
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
                'edit'=>U('editRole?id='.$value['id']),
                'delete'=>U('deleteRole?id='.$value['id']),
                'people'=>U('people?id='.$value['id']),
                );
        }
        return $data;
    }
    private funcction _permissionList(){
        $menuModel = new MenuModel();
        $permissionList = $menuModel ->getMenuTree();
    }
}
