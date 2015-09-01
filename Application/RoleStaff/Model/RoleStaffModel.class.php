<?php

/*
 * 角色与教工的对应的Model类
 * @author xuao
 * 295184686@qq.com
 */
namespace RoleStaff\Model;
use Think\Model;
use StaffManagement\Model\StaffManagementModel;
class RoleStaffModel extends Model{
	/**
	 * [getStaffListByRoleId 通过角色id获取该角色中的教工列表]
	 * @param  [string] $roleId [角色id]
	 * @return [array]         [教工列表]
	 */
	public function getInRoleStaffByRoleId($roleId){
		//从角色-教工表中获取教工id
		$map['role_id'] = $roleId;
		$data = $this -> where($map) ->field('staff_id')-> select();
		$data = change_key($data,'staff_id');

		//根据教工id获取教工信息
		$staffModel = new StaffManagementModel;
		foreach ($data as $key => $value) {
			$data[$key] = $staffModel->getStaffById($value['staff_id']);
		}
		return $data;
	}
	public function getOutRoleStaffByRoleId($roleId){
		//获取总的教工列表
		$staffModel = new StaffManagementModel;
		$data = $staffModel->getStaffList();
		var_dump($data);
		exit();
		//获取该角色中的教工列表
		$inRoleData = $this -> getgetInRoleStaffByRoleId($roleId);
		//从总的教工列表中去除角色中的教工
		//获得没有在 角色中的教工列表


	}
}