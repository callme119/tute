<?php
/**
 * 用户 部门 岗位表 -- 部门岗位表 -- 用户表-- 部门表 -- 岗位表
 * 联合查询
 */
namespace UserDepartmentPost\Logic;
use UserDepartmentPost\Model\UserDepartmentPostViewModel;
class UserDepartmentPostViewLogic extends UserDepartmentPostViewModel
{
	public function getAllListsByUserId($userId)
	{
		$userId = (int)$userId;
		$map['user_id'] = $userId;
		$return = $this->where($map)->select();
		return $return;
	}
}