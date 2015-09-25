<?php

namespace PersonalCenter\Controller;

use Admin\Controller\AdminController;
use UserDepartmentPost\Model\UserDepartmentPostModel;
use Role\Model\RoleModel;
use User\Model\UserModel;

class IndexController extends AdminController {

    public function indexAction() {
        $id = get_user_id();
        //取部门岗位
        $model = new UserDepartmentPostModel;
        $lists = $model->getDepartmentPostInfoListsById($id);
        //取角色
        $RoleModel = new RoleModel;
        //取名称
        $role = $RoleModel->getRoleNameByUserId($id);
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
