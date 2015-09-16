<?php
/**
 * 获取用户信息
 */
namespace Task\Widget;
use Think\Controller;
use User\Logic\UserLogic;		//用户
class UserWidget extends Controller
{
	/**
	 * 根据传入的user数据，返回相关可供点击的checkbox
	 * @param  array $users 一维数据
	 * @return string        html字符串
	 */
	public function getUserCheckBoxByUserIdAction($userId, $name= "user_id" , $class="user")
	{

		//取用户信息
		$userId = (int)$userId;
		$UserL = new UserLogic();

		//判断是否存在用户信息，且用户信息正常
		if(!($user = $UserL->getListById($userId)))
		{
			return;
		}

		//用户如果为冻结，退出 
		if($user['state'] == '0')
		{
			return;
		}
	
		//传值
		$this->assign("name",$name);
		$this->assign("class",$class);
		$this->assign("user",$user);
		$this->display("Widget/getUserCheckBoxs");
	}
}