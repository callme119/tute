<?php
/**
 * 角色
 */
namespace Model;
use Role\Model\RoleModel;               // 角色表

class Role extends Model {
    private $isAdmin;           // 是否管理员

    public function __construct($id = 0) {
        $map = array('id'=> (int)$id);
        $RoleModel = new RoleModel;
        $data = $RoleModel->where($map)->find();
        if (!is_null($data)) {
            $this->data = $data;
        }
        unset($RoleModel);
    }

    /**
     * 是否管理员
     * @return   boolean                  是:true 否：false
     * @author 梦云智 http://www.mengyunzhi.com
     * @DateTime 2016-12-06T12:58:06+0800
     */
    public function isAdmin() {
        if (is_null($this->isAdmin)) {
            $flag = (int)$this->getData('is_admin');
            if ($flag) {
                $this->isAdmin = true;
            } else {
                $this->isAdmin = false;
            }
        }

        return $this->isAdmin;
    }
}