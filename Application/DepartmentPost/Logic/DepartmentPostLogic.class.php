<?php
/**
 * 部门岗位表 LOGIC
 */
namespace DepartmentPost\Logic;
use DepartmentPost\Model\DepartmentPostModel;		//部门岗位M
class DepartmentPostLogic extends DepartmentPostModel
{
	public function getAllListsByDepartmentId($departmentId , $state = 1)
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

	public function getListByid($id)
	{
		$id = (int)$id;
		$map['id'] = $id;
		$return = $this->where($map)->find();
		return $return;
	}

}