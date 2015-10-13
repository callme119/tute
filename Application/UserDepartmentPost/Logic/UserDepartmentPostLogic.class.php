<?php
/**
 * 用户部门岗位 L
 */
namespace UserDepartmentPost\Logic;
use UserDepartmentPost\Model\UserDepartmentPostModel;
class UserDepartmentPostLogic extends UserDepartmentPostModel
{
	public function getListByUserIdDepartmentPostId($userId , $departmentPostId)
	{
		$map['user_id'] = $userId;
		$map['department_post_id'] = $departmentPostId;
		$return = $this->where($map)->find();
		return $return;
	}

	public function save($data)
	{
		if($this->create($data))
		{
			$this->add();
		}
		else
		{
			$this->error = "数据create发生错误";
			return false;
		}
	}

	public function deleteById($id)
	{
		$id = (int)$id;
		$map['id'] = $id;
		$this->where($map)->delete();
		return true;
	}

	public function deleteByUserId($userId)
	{
		$userId = (int)$userId;
		$map['user_id'] = $userId;
		$this->where($map)->delete();
		return true;
	}
}