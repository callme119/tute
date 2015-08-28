<?php

/*
 * 角色与菜单的对应的Model类（也就是权限表）
 *
 * @author xuao
 * 295184686@qq.com
 */
namespace RoleMenu\Model;
use Think\Model;
class RoleMenuModel extends Model{


	/**
	 * [getMenuByRoleId 通过角色id获取相对应的菜单列表]
	 * @param  [string] $roleId [角色id]
	 * @return [array]         [获取到的菜单列表数组]
	 */
	public  function getMenuByRoleId($roleId){
		$map['role_id'] = $roleId;
		$menuData = $this->where($map)->select();
		return $menuData;
	}

	/**
	 * [updateMenuAndRole 更新menu-role数据]
	 * @return [type] [description]
	 */
	public function updateMenuAndRole(){
		$data = I('post.');
		//清空原有数据
		$map['role_id'] = $data['id'];
		$this-> where($map)->delete();

		//获取新的数据
		$permissionInfo = array();
		foreach ($data as $key => $value) {
			if($value == 'on'){
				$permissionInfo[] = array('role_id' => $data['id'],'menu_id' => $key,'value' => 1);
			}
		}
		//更新
		$this->addAll($permissionInfo);
		return true;
	}
	public function saveMenuAndRole(){
		$data = I('post.');
		$permissionInfo = array();
		foreach ($data as $key => $value) {
			if($value == 'on'){
				$permissionInfo[] = array('role_id' => $data['id'],'menu_id' => $key,'value' => 1);
			}
		}
		//更新
		$this->addAll($permissionInfo);
		return true;

	}
}