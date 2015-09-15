<?php
/*教工管理模块
 * author:weijingyun
 * email:1028193951@qq.com
 * create date:2015.07.16
 */
namespace User\Controller;
use Admin\Controller\AdminController;
use User\Model\UserModel;
use Role\Model\RoleModel;
use Department\Model\DepartmentModel;
use Post\Model\PostModel;
use RoleUser\Model\RoleUserModel;
class IndexController extends AdminController {

    //教工列表显示
    public function indexAction(){
        //获取教工列表
        $staffModel = new UserModel();
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
        //获取部门列表信息(通过私有防发获取)
        //获取岗位列表信息(通过私有防发获取)
        //获取角色列表信息(通过私有防发获取)
        //传值，前台进行处理
        $url = U('save');
        $this->assign('url',$url);

        //传递角色列表（添加教工的角色复选框）
        $this -> assign('roleList',$this -> _fetchRoleList());
        
        $this->assign('css',$this->fetch("addCss"));
        $this->assign('js',$this->fetch("addJs"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教工管理编辑
    public function editAction(){
        $id = I('get.id');
        //获取当前教工的信息
        $staffModel = new UserModel();
        $staffInfo = $staffModel -> getStaffById($id);
        $this ->assign('staffInfo',$staffInfo);

        //设置url
        $url = U('update?id='.$id);
        $this->assign('url',$url);

        //传递角色列表（编辑教工的角色复选框）
        $this -> assign('roleList',$this -> _fetchRoleList());

        $this->assign('css',$this->fetch("addCss"));
        $this->assign('js',$this->fetch("addJs"));
        $this->assign('YZBODY',$this->fetch('add'));
        $this->display(YZTemplate);  
    }
    //删除教工
    public function deleteAction(){
        //删除该教工
        //删除该教工与角色的对应信息
        //删除该教工与部门岗位的对应信息
        $id = I('get.id');
        $staffModel = new UserModel();
        $state = $staffModel -> deleteStaff($id);
        if($state){
            $this->success('删除成功', U('index'));
        }
    }
    //添加教工完成
    public function saveAction(){
        $staffModel = new UserModel();
        $state = $staffModel -> addStaff();

        if($state){
            $this->success('新增成功', 'index');
        }
    }
    //编辑教工完成
    public function updateAction(){
        $staffModel = new UserModel();
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

    //修改用户邮箱，手机号，密码等个人信息
    public function changeUserInfoAction(){
        $model = new UserModel;
        $newpsw = I('post.newpsw');
        $oldpsw = I('post.oldpsw');
        $userId = I('get.id');
        //先进行密码验证
        if($model->checkPsw($oldpsw,$userId)){
            $this->error('密码错误，请重新输入');
        }; 
        
        //进行分类，如果新密码为空，我们认为是仅修改邮箱或手机
        //不为空，我们认为修改密码
        if($newpsw == 0){
            if($model->changePhoneOrEmail($userId)){
                $this->success('修改信息成功');
            }     
        }else{
            if($model->changePsw($newpsw,$userId)){
                $this->success('修改密码成功');
            }
        };
    }
    /**
     * [_fetchRoleList 获取角色列表传递到前台]
     * @return [type] [角色列表]
     */
    public function _fetchRoleList(){
        $roleModel = new RoleModel;
        $roleList = $roleModel -> getRoleList(1);
        return $roleList;
    }
}