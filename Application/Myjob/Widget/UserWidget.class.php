<?php
namespace Myjob\Widget;
use User\Model\UserModel;
class UserWidget {
    public function getUserByIdAction($id = null){
        if($id === null)
        	return false;
        $userM = new UserModel();
        $data = $userM->getUserById($id);
        return $data;
    }

    public function getUserNameByIdAction($id = null){
        $data = $this->getUserByIdAction($id);
        return $data['name'];
    }
}