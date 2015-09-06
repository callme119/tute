<?php

/*
 * 角色与教工的对应的Model类
 * @author xuao
 * 295184686@qq.com
 */
namespace RoleStaff\Model;
use Think\Model;
use User\Model\UserModel;
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
		$staffModel = new UserModel;
		foreach ($data as $key => $value) {
			$data[$key] = $staffModel->getStaffById($value['staff_id']);
		}
		return $data;
	}

	public function getOutRoleStaffByRoleId($roleId){
		//获取总的教工列表
		$staffModel = new UserModel;
		$data = $staffModel->getStaffList();
		$data = change_key($data,'id');

		//获取该角色中的教工列表
		$inRoleData = $this -> getInRoleStaffByRoleId($roleId);

		//从总的教工列表中去除角色中的教工
		if(!empty($inRoleData)){
			foreach ($inRoleData as $key1 => $value1) {
				unset($data[$key1]);
			}
		}
		
		//获得没有在 角色中的教工列表
		return $data;
	}

	public function saveRoleStaff(){
		//接收数据
		$data['role_id'] = I('get.roleId');
		$data['staff_id'] = I('get.staffId');
		//保存数据
		$this->add($data);
		return ture;
	}

	public function deleteRoleStaff(){
		//接收数据
		$data['role_id'] = I('get.roleId');
		$data['staff_id'] = I('get.staffId');
		//删除数据
		$this->where($data)->delete();
		return ture;
	}
}