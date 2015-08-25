<?php

/*
 *
 * @author xuao
 */
namespace Role\Controller;
use Admin\Controller\AdminController;
use Role\Model\RoleModel;
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
     * @param  [type]
     * @param  [type]
     * @return [type]
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
}
