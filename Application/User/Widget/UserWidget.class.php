<?php
namespace user\Widget;
use User\Model\UserModel;
use UserDepartmentPost\Model\UserDepartmentPostModel; //员工部门岗位表

class UserWidget
{
	/**
	 * 显示用户的职位信息
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function showDepartmentPostByIdAction($id)
	{
		if(!is_numeric($id))
		{
			echo "";
			return;
		}

		$UserDepartmentPostM = new UserDepartmentPostModel();
		$userDepartmentPosts = $UserDepartmentPostM->getDepartmentPostInfoListsById($id);
		$i = 0;
		$string = "";
		foreach($userDepartmentPosts as $userDepartmentPost)
		{
			if($i++)
			{
				$string .= "<br/>";
			}
			$string .= '<span class="department_name">';
			$string .= $userDepartmentPost["department_name"];
			$string .= '</span>';
			$string .= '<span class="post_name">[';
			$string .= $userDepartmentPost['post_name'];
			$string .= ']</span>';
		}

		echo $string;
	}

	public function getNameByIdAction($id)
	{
		$UserM = new UserModel();
		$map['id'] = $id;
		$user = $UserM->where($map)->find();
		echo $user['name'];
	}
}
