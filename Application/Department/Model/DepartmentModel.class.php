<?php
namespace Department\Model;
use Think\Model;
class DepartmentModel extends Model
{
	/**
	 * 通过部门ID，返回本部门及上级部门组成的列表。
	 * @param  array $currentDepartments 包括有 部门ID 的多维数组
	 * @param  string $keyWord            keywork下标
	 * @return 数组                     [一维数组，只包括返回数组的ID]
	 */
	public function getParentTreeByLists($currentDepartments , $keyWord = "department_id")
	{
		$return = array();
		foreach($currentDepartments as $currentDepartment)
		{
			$return[] = $this->getParentTreeByList($currentDepartment , $keyWord);
		}
		return $return;


	}
	/**
	 * 通过包括有部门关键字的数据，获取该部门及上级部门的ID
	 * @param  [array] $currentDepartment [一组数据]
	 * @param  string $keyWord           [关键字]
	 * @return [array]                    [本级及上级部门树]
	 */
	public function getParentTreeByList($currentDepartment , $keyWord = "department_id")
	{
		$parentId = $currentDepartment[$keyWord];
		do
		{
			$map[id] = $parentId;
			$data = $this->where($map)->find();
			if(count($data))
			{
				$return[$data[id]] = $data[id];
				$parentId = $data['parent_id'];
			}
			else
			{
				$parentId = "0";
			}
		}
		while($parentId != "0" );
		return $return;
	}
}