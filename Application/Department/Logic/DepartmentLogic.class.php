<?php
/**
 * 部门表 logic
 */
namespace Department\Logic;
use Department\Model\DepartmentModel;
class DepartmentLogic extends DepartmentModel
{
	public function getAllLists($status = 0)
	{
		if(0 <= $status && $status < 2)
		{
			$map['status'] = $status;
		}
		$return = $this->where($map)->select();
		return $return;
	}
}