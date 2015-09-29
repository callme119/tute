<?php
/**
 * 用户 部门 岗位表 -- 部门岗位表 -- 用户表-- 部门表 -- 岗位表
 * 联合查询
 */
namespace UserDepartmentPost\Model;
use Think\Model\ViewModel;
class UserDepartmentPostViewModel extends ViewModel
{
	public $viewFields = array(
		'UserDepartmentPost' 	=> array('id' , 'user_id' , "department_post_id"),
		'DepartmentPost'		=> array("_on" => "UserDepartmentPost.department_post_id=DepartmentPost.id"),
		'Department'			=> array("name" => "department_name" , "_on" => "DepartmentPost.department_id = Department.id" ),	
		'Post'					=> array("name" => "post_name" , "_on" => "DepartmentPost.post_id = Post.id"),
	);
}
