<?php
/**
 * 用户
 */
namespace Model;
use User\Model\UserModel;
use UserRole\Model\UserRoleModel;
class User extends Model {
    private $Roles;                             // 角色 N:N
    private $isAdmin;                           // 是否为管理员
    static public function get($mix) {
        if (!is_array($mix)) {
            if (is_numeric($mix)) {
                $mix = array('id' => $mix);
            } else if (!is_string($mix)) {
                $mix = array();
            }
        }

        $UserModel = new UserModel;
        $data = $UserModel->where($mix)->find();
        $self = new self();
        if (!is_null($data)) {
            $self->setData($data);
        }
        return $self;
    }

    /**
     * 是否管理员
     * @return   boolean                  是:true; 否:false;
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T12:58:39+0800
     */
    public function isAdmin() {
        if (is_null($this->isAdmin)) {
            $this->isAdmin = false;

            // 依次对所属的角色进行判断
            foreach ($this->Roles() as $Role) {
                if ($Role->isAdmin()) {
                    $this->isAdmin = true;
                    break;
                }
            }
        }

        return $this->isAdmin;
    }

    /**
     * 角色 N:N
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T12:59:15+0800
     */
    public function Roles() {
        if (is_null($this->Roles)) {
            $this->Roles = array();
            $UserRoleModel = new UserRoleModel;
            $map = array('user_id'=> $this->getData('id'));
            $datas = $UserRoleModel->where($map)->select();
            foreach ($datas as $data) {
                array_push($this->Roles, new Role($data['role_id']));
            }
            unset($UserRoleModel);
        }
        return $this->Roles;
    }

    /**
     * 用户注销
     * @return   true                   
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T13:22:29+0800
     */
    static public function logout() {
        session('user_id',null);
        session('user_name',null);
        return true;
    }
}