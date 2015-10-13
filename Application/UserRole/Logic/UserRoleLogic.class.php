<?php
namespace UserRole\Logic;
use UserRole\Model\UserRoleModel;
class UserRoleLogic extends UserRoleModel
{
	public function deleteByUserId($userId)
	{
		$userId = (int)$userId;
		$map['user_id'] = $userId;
		$this->where($map)->delete();
		return true;
	}
}