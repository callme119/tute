<?php
/**
 * 岗位设置
 * panjie 
 * 3792535@qq.com
 */
namespace User\Controller;
use UserDepartmentPost\Logic\UserDepartmentPostViewLogic;		//用户部门岗位表联合查询
use UserDepartmentPost\Logic\UserDepartmentPostLogic;		//用户部门岗位
use User\Logic\UserLogic;		//用户表
use Department\Logic\DepartmentLogic;		//部门表
use DepartmentPost\Logic\DepartmentPostLogic;		//部门岗位表
use DepartmentPost\Logic\DepartmentPostViewLogic;		//部门岗位-视图
use Admin\Controller\AdminController;
class PostController extends AdminController
{
	public function indexAction()
	{
		try
		{	
			//取用户信息
			$userId = (int)I('get.user_id');
			if($userId == 0)
			{
				E("传入的userId:$userId,有误");
			}

			$UserL = new UserLogic();
			$user = $UserL->getListById($userId);
			if($user == null)
			{
				E("未找到userid为$userId的相关记录");
			}

			//取用户部门岗位信息
			$UserDepartmentPostViewL = new UserDepartmentPostViewLogic();
			$userDepartmentPosts = $UserDepartmentPostViewL->getAllListsByUserId($userId);
			
			//取部门信息
			$DepartmentL = new DepartmentLogic();
			$departments = $DepartmentL->getAllLists();
			
			//传值
			$this->assign("userId",$userId);
			$this->assign("userDepartmentPosts",$userDepartmentPosts);
			$this->assign("departments",$departments);
			$this->assign("js",$this->fetch('indexJs'));
			$this->assign("YZBODY",$this->fetch());
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	public function saveAction()
	{	
		try
		{
			$userId = (int)I('post.user_id');
			$departmentPostId = (int)I('post.department_post_id');

			//查询是否有当前用户
			$UserL = new UserLogic();
			$user = $UserL->getListById($userId);
			if($user == null)
			{
				E("您要设置的用户不存在,或传入了错误的用户ID:$userId");
			}

			//查询是否有当前 部门岗位 ID
			$DepartmentPost = new DepartmentPostLogic();
			$departmentPost = $DepartmentPost->getListByid($departmentPostId);
			if($departmentPost == null)
			{
				E("您要设置的部门岗位不存在,或传入了错误的部门岗位ID:$departmentPostId");
			} 

			//查询用户是否已经拥有本岗位
			$UserDepartmentPostL = new UserDepartmentPostLogic();
			$userDepartmentPost = $UserDepartmentPostL->getListByUserIdDepartmentPostId($userId , $departmentPostId);

			//没有,则进行添加操作
			if($userDepartmentPost == null)
			{
				$UserDepartmentPostL->add(I('post.'));
			}

			//返回当前用户
			$this->success("操作成功",U('index',I('get.')));
			
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
		
	}

	public function deleteAction()
	{
		try
		{
			$userDepartmentPostId  = (int)I('get.user_department_post_id');

			$UserDepartmentPostL = new UserDepartmentPostLogic();
			$UserDepartmentPostL->deleteById($userDepartmentPostId);

		//返回当前用户
		$this->success("操作成功",U('index',I('get.')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
	/**
	 * 通过传入的部门ID,获取该部门下的所有岗位信息
	 * @return array ajax
	 */
	public function getPostsByDepartmentIdAjaxAction()
	{
		$state = "success";
		$data  = array();

		$departmentId = (int)I('get.department_id');

		$DepartmentPostViewL = new DepartmentPostViewLogic();
		$departmentPosts = $DepartmentPostViewL->getAllListsByDepartmentId($departmentId);

		$return['state'] = $state;
		$return['data'] = $departmentPosts;
		return $this->ajaxReturn($return);
	}
}