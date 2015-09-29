<?php
/**
 * 部门岗位 -- 部门 -- 岗位 视图logic
 */
namespace DepartmentPost\Logic;
use DepartmentPost\Model\DepartmentPostViewModel;		//m层
class DepartmentPostViewLogic extends DepartmentPostViewModel
{
	public function getAllListsByDepartmentId($departmentId, $state = 1)
	{
		$state = (int)$state;
		if($state >= 0 && $state < 2)
		{
			$map['state'] = $state;
		}

		$map['department_id'] = (int)$departmentId;

		$return = $this->where($map)->select();
		// echo $this->getLastSql();
		return $return;
	}
}