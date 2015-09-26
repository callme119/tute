<?php

/*
 * Post逻辑层，主要用来处理菜单删除部分的相关逻辑
 * @author xuao
 * 295184686@qq.com
 */
namespace Post\Logic;
use Think\Model;
use Post\Model\PostModel;
use DepartmentPost\Model\DepartmentPostModel;
class PostLogic{
	/**
	 * [beDelete 判断岗位是否可以被删除，判断依据是与该岗位关联的部门-岗位中是否有用户]
	 * 如果有，就不能删除，如果没有，可以删除
	 * @param  [type] $postId [postId]
	 * @return [type]         [如果可以删除，返回true，如果不能删除，返回false]
	 */
	public function beDelete($postId){
		$map['post_id'] = $postId;
		$departmentPostModel = new DepartmentPostModel();
		$postDepartmentUser = $departmentPostModel -> where($map) -> join(
			'yunzhi_user_department_post ON yunzhi_department_post.id = yunzhi_user_department_post.department_post_id')->find();
		$result = empty($postDepartmentUser);
		return $result;
	}
}