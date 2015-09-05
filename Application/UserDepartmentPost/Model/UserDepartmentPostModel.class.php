<?php
namespace UserDepartmentPost\Model;
use Think\Model;
class UserDepartmentPostModel EXTENDS Model{
	/**
	 * 通过userid及postid信息，查看用户在这个postid下，有几个部门，并返回
	 * @param  string $userId userid
	 * @param  stirng $postId postid
	 * @return array         包括所有部门信息的数组
	 * author:panjie 3792535@qq.com
	 */
	public function getListsByUseridAndPost($userId = null, $postId = null)
	{

		if($userId === null || $postId === null || !is_numeric($userId) || !is_numeric($postId))
		{
			$this->error = "传入参数有误";
			return false;
		}

		$map['user_id'] = $userId;
		$map['post_id'] = $postId;

		$return = $this->where($map)->join("LEFT JOIN __DEPARTMENT_POST__ ON __USER_DEPARTMENT_POST__.department_post_id = __DEPARTMENT_POST__.id")->select();
		// echo $this->getLastSql();
		
		if(count($return) == 0)
		{
			echo "hello";
			$this->error = "当前流程的执行人员，部门或岗位发生变化，该流程无法正常执行。";
			return false;
		}
		return $return;
	}

	/**
	 * 通过数组（一维数组，存的为部门ID值—），岗位ID，获取部门信息的岗位值。
	 * @param  array $lists  部门信息,array
	 * @param  岗位ID $postId [description]
	 * @return [type]         [description]
	 */
	public function getListsByListsAndPostId($lists , $postId)
	{

		return;
	}

	/**
	 * 通过department_post_id获取用户列表。有数据时则返回。
	 * 目的：一个用户多个岗位时，正确的返回审核用户。
	 * @param  array $departmentPostlists 一组数据
	 * @return array                      二维数据，返回所有信息
	 */
	public function getFirstListsByDepartmentPostIds($departmentPostlists)
	{
		$data = array();
		foreach($departmentPostlists as $key => $value)
		{
			$map['department_post_id'] = $value;
			$data = $this->where($map)->select();
			if(count($data))
			{
				return $data;
			}
		}
		return $data;
	}
	/**
	 * 通过USERID获取列表数据
	 * @param  string $userId 用户ID
	 * @return array         二维数组
	 */
	public function getListsByUserId($userId = null)
	{
		if($userId === null || !is_string($userId))
		{
			$this->error = "userID未传入或传入的类型有误";
		}

		$map[user_id] = $userId;
		//查询数据
		$data = $this->where($map)->join("left join __DEPARTMENT_POST__ on __USER_DEPARTMENT_POST__.department_post_id = __DEPARTMENT_POST__.id")->select();
		return $data;	
	}
}