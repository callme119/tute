<?php

/*
 * 角色与菜单的对应的Model类（也就是权限表）
 *
 * @author xuao
 * 295184686@qq.com
 */
namespace RoleMenu\Logic;
use RoleMenu\Model\RoleMenuModel;
class RoleMenuLogic extends RoleMenuModel{
	public function getMenuListByRoleList($roleIdList){
		$roleIds  = $roleIdList;
		$menuList = array();
		$res = array();
		if($roleIds){
			foreach ($roleIds as $key => $value) {
			$menuList = $this ->getMenuListByRoleId($value['role_id']);
			//$menuList = change_key($menuList,'id');
			$res = union_array($res,$menuList);
			}
		}
		return $res;
		
	}

	public function getListByRoleIdMenuId($roleId , $menuId , $value = 1)
	{
		$roleId = (int)$roleId;
		$menuId = (int)$menuId;
		$map = array();
		$map['role_id'] = $roleId;
		$map['menu_id'] = $menuId;
		$map['value'] = $value;
		$data = $this->where($map)->find();
		return $data;
	}

}