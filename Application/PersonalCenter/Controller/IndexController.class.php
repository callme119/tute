<?php

namespace PersonalCenter\Controller;

use Admin\Controller\AdminController;
use UserDepartmentPost\Model\UserDepartmentPostModel;
use UserRole\Model\UserRoleModel;
use Role\Model\RoleModel;
use User\Model\UserModel;

class IndexController extends AdminController {

    public function indexAction() {
        $id = get_user_id();
        //取部门岗位
        $model = new UserDepartmentPostModel;
        $lists = $model->getDepartmentPostInfoListsById($id);
        
        //取角色
        $UserRoleModel = new UserRoleModel;
        $role = array();
        $roleIds = $UserRoleModel->getRoleIdListByUserId($id);
        $RoleModel = new RoleModel;
        foreach ($roleIds as $key => $value) {
            $role[] = $RoleModel->getRoleById($value['role_id'])['name'];
        }

        //取名称
        $user = new UserModel;
        $info = $user->getUserById($id);

        $this->assign('info',$info);
        $this->assign('role',$role);
        $this->assign('lists',$lists);
        $this->assign('css',$this->fetch(T('indexCss')));
        $this->assign('js',$this->fetch(T('indexJs')));
//        $index = $this->fetch(T('index'));
//        $this->show($index);
        $this->assign('YZBODY', $this->fetch());
        $this->display(YZTemplate);
    }

}
