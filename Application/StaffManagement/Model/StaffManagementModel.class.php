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
	public function getStaffList(){
		$data = $this -> select();
		return $data;
	}
	//添加教工
	public function addStaff(){
		$data = I('post.');
		$this->add($data);
		return true;
	}
}