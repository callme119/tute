<?php
/**
 * 部门 岗位 -- 部门 -- 岗位联结表
 */
namespace DepartmentPost\Model;
use Think\Model\ViewModel;
class DepartmentPostViewModel extends ViewModel
{
	public $viewFields = array(
		'DepartmentPost' 	=> array('id','department_id','post_id' , 'state'),
		'Post'				=> array('name' => 'post_name' , "_on" => 'DepartmentPost.post_id = Post.id'),
		'Department'		=> array('name' => 'department_name' , "_on" => 'DepartmentPost.department_id = Department.id'),
	);

}