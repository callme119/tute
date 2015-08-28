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
	 * [updateMenuAndRole 更新menu-role数据]
	 * @return [type] [description]
	 */
	public function updateMenuAndRole(){
		//清空原有数据
		
		//获取新的数据
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
	public function saveMenuAndRole(){
		return true;

	}
}
