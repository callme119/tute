<?php

/*
 * 教工的Model类
 *
 * @author xuao
 * 295184686@qq.com
 */
namespace StaffManagement\Model;
use Think\Model;
class StaffManagementModel extends Model{
	//获取教工列表
	public function getStaffList(){
		$data = $this -> select();
		return $data;
	}
	//获取固定id的教工的信息
	public function getStaffById($id){
		$map['id'] = $id;
		$data = $this -> where($map) ->find();
		return $data;
	}
	//添加教工
	public function addStaff(){
		$data = I('post.');
		$this->add($data);
		return true;
	}
	//编辑教工
	public function updateStaff(){
		$data = I('post.');
		$data[id] = I('get.id');
		$this -> save($data);
		return true;
	}
	public function deleteStaff(){
		$data[id] = I('get.id');
		$state = $this -> where($data) ->delete();
		return $state;
	}
}