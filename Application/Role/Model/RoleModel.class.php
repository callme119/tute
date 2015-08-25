<?php

/**
 * 角色的Model类
 *
 * @author xuao
 * 295184686@qq.com
 */
namespace Role\Model;
use Think\Model;
class RoleModel extends Model{
    /**
     * 获取角色列表方法
     * 
     * @return array
     */
    public function getRoleList($page){
    	$res = $this->select();
    	return $res;
    }
}