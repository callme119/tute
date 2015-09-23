<?php

/*
 * 角色与教工的对应的Model类
 * @author xuao
 * 295184686@qq.com
 */
namespace RoleUser\Model;
use Think\Model;
use User\Model\UserModel;
class RoleUserModel extends Model{

	public function getRoleIdListByUserId($userId){
		$map['user_id'] = $userId;
		$roleIdList = $this -> where($map) -> select();
		return $roleIdList;
	}
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
	/**
	 * [saveRoleUser 更新用户-角色信息]
	 * @return [type] [true]
	 */
	public function saveRoleUser(){
		$data = I('post.');
		//清空原有数据
		$map['user_id'] = $data['id'];
		$this-> where($map)->delete();

		//获取新的数据
		$userRole = array();
		foreach ($data as $key => $value) {
			if($value == 'on'){
				$userRole[] = array('user_id' => $data['id'],'role_id' => $key,'state' => 1);
			}
		}
		//更新
		$this->addAll($userRole);
		return true;
	}
	/**
	 * [addRoleUser 添加用户-角色信息]
	 */
	public function addRoleUser(){
		$data = I('post.');
		//获取新的数据
		$userRole = array();
		foreach ($data as $key => $value) {
			if($value == 'on'){
				$userRole[] = array('user_id' => $data['id'],'role_id' => $key,'state' => 1);
			}
		}
		//更新
		$this->addAll($userRole);
		return true;
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