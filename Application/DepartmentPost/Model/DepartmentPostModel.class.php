<?php
namespace DepartmentPost\Model;
use Think\Model;
use Department\Model\DepartmentModel;
class DepartmentPostModel EXTENDS Model
{
	/**
	 * 通过部门数组及岗位ID，返回符合条件的 部门岗位ID的数组。
	 * @param  array $departments 一组数组，每项为KEY
	 * @param  string $postId      岗位ID
	 * @return array              一维数组，返回符合的岗位信息
	 */
	public function getListByDepartmentsAndPost($departments,$postId)
	{
		$return = array();
		foreach($departments as $key => $value)
		{
			$map['department_id'] = $value;
			$map['post_id']	= $postId;
			$data = $this->where($map)->find();
			if(!$data == null)
			{
				$return[$data[id]] = $data[id];
			}
		}
		return $return;
	}
	/**
	 * [getDepartmentPostInfoByDepartId 通过部门id获取该部门下的岗位列表]
	 * @param  [type] $departmentId [部门id]
	 * @return [type]               [部门-岗位信息]
	 * 部门模块，部门编辑复选框需要调用的数据
	 */
	public function getDepartmentPostInfoByDepartId($departmentId){
		$map['department_id'] = $departmentId;
		$data = $this -> where($map) -> select();

		return $data;
	}


	public function addDepartmentPost(){
		$data = I('post.');
		//获取部门-岗位信息
		$department_post = array();
		foreach ($data as $key => $value) {
			if($value == 'on'){
				$department_post[] = array('department_id' => $data['id'],'post_id' => $key,'state' => 1);
			}
		}
		//保存信息
		$this->addAll($department_post);
		return true;
	}
	public function updataDepartmentPost(){
		//删除原来该部门下的岗位信息
		$data = I('post.');
		$map['department_id'] = $data['id'];
		$initialdata = $this-> where($map)->select();
		$departmentModel = new DepartmentModel;
		$hasPostIdList = $departmentModel -> checkUserByDepartmentId($data['id']);
		foreach ($initialdata as $key => $value) {
			if(!in_array($value['post_id'], $hasPostIdList)){
				$this -> where('id='.$value['id']) ->delete();
			}
		}
		//添加现有的部门岗位信息
		$department_post = array();
		foreach ($data['postId'] as $key => $value) {
			$department_post[] = array('department_id' => $data['id'],'post_id' => $value,'state' => 1);
		}
		//保存信息
		$this->addAll($department_post);
		return true;
	}
}