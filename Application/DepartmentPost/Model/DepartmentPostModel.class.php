<?php
namespace DepartmentPost\Model;
use Think\Model;
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
			if(is_array($data))
			{
				$return[$data[id]] = $data[id];
			}
		}
		return $return;
	}
}