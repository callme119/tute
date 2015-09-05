<?php
/**
 * 审核结点
 */
namespace Chain\Logic;
use Think\Model;
use UserDepartmentPost\Model\UserDepartmentPostModel;
use Department\Model\DepartmentModel;//部门表
use DepartmentPost\Model\DepartmentPostModel;//部门 岗位 对应表
class ChainLogic EXTENDS Model
{
	/**
	 * 通过用户ID和审核名细ID，获取下一审核结点的人员信息。
	 * @param  string $userId   用户ID
	 * @param  string $id 审核结点ID
	 * @return (boon)array          
	 * 错误：false.
	 * 为最后结点：success.
	 * 二维数组：审核人员信息（用户ID，用户姓名）
	 */
	public function getNextExaminUsersByUserIdAndId($userId , $id)
	{
		//获取当前结点的岗位ID
		$map['id'] = $id;
		$data = $this->where($map)->find();
		if($data == null)
		{
			$this->error = "传入了错误的ID值，此ID在数据库中不存在";
			return false;
		}
		if($data['next_id'] == 0)
			return true;
		$currentPostId = $data['now_post'];
		
		//获取下一结点岗位ID
		$map['id'] = $data['next_id'];
		$data = $this->where($map)->find();
		if($data == null)
		{
			$this->error = "系统发生错误，该错误可能是在审核流程存储时发生的";
			return false;
		}
		$nextPostId = $data['now_post'];

		//取当前用户当前岗位对应的本部门列表（可能是多个部门相同岗位，二维数组）
		$UserDepartmentPostM = new UserDepartmentPostModel();
		$departments = $UserDepartmentPostM->getListsByUseridAndPost($userId , $currentPostId);
		if(!$departments)
		{
			$this->error = $UserDepartmentPostM->getError();
			return false;
		}

		//获取各个部门的部门TREE
		$departmentM = new DepartmentModel();
		$parentDepartmentsTreeArrays = $departmentM->getParentTreeByLists($departments , "department_id");
 		
        // 1 step.分别取部门列表下，存在的下一审核岗位信息。
        // 2 step.分别取当前部门、岗位下的人员，有则直接返回第一个部门下的列表。
        $DepartmentPostM = new DepartmentPostModel();
        foreach($parentDepartmentsTreeArrays as $parentDepartmentsTreeArray)
        {
        	//1 step
        	$departmentPosts = $DepartmentPostM->getListByDepartmentsAndPost($parentDepartmentsTreeArray,$nextPostId);
        	
        	//2 step
        	$userLists[] = $UserDepartmentPostM->getFirstListsByDepartmentPostIds($departmentPosts);
        }

        //去除重复用户（A,B两人都是两个部门，两个岗位，且岗位相同时，会触发）
        $users = array();
        foreach($userLists as $value)
        {
        	foreach($value as $v)
        	{
        		$users[$v['user_id']] = $v;
        	}
        }

		return $users;
	}
}