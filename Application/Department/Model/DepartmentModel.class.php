<?php
namespace Department\Model;
use Think\Model;
class DepartmentModel extends Model
{
	public function getDepartmentTree($rootDepatrmentId = 0,$layer,$keyWord = '_son'){
		//设置根元素id,获取一级元素
		$map['parent_id'] = $rootDepatrmentId;
		$data = $this -> where($map) -> select();

		//根据层级获得树形结构的所有元素
		if($layer--){
			foreach ($data as $key => $value) {
				//以一级元素为根元素查找新的树
				$rootDepatrmentId = $value['id'];
				//通过/递归找到所有元素
				$data[$key][$keyWord] = $this -> getDepartmentList($rootDepatrmentId,$layer,'_son');
			}
		}

		return $data;
		
	}


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