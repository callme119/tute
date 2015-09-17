<?php
/**
 * 获取用户信息
 */
namespace BaseTeaching\Widget;
use Think\Controller;
use User\Logic\UserLogic;		//用户
class UserWidget extends Controller
{
	/**
	 * 根据传入的user数据，返回相关可供点击的checkbox
	 * @param  array $users 一维数据
	 * @return string        html字符串
	 */
	public function getUserCheckBoxByUserIdUserNameAction($userId, $userName, $name= "user_id" , $class="user")
	{
		//传值
		$this->assign("name",$name);
		$this->assign("class",$class);
		$this->assign("userId",$userId);
		$this->assign("userName",$userName);
		$this->display("Widget/getUserCheckBoxs");
	}

	/**
	 * 获取用户的姓名
	 * @param  int $id           用户ID
	 * @param  string $defaultValue 如果没有该用户，显示的默认值
	 * @return string               用户的名字
	 */
	public function getNameByIdAction($id , $defaultValue = '-')
	{
		$id = (int)$id;
		$UserL = new UserLogic();
		$user = $UserL->getListById($id);
		echo isset($user['name'])? $user['name'] : $defaultValue;
	}
}