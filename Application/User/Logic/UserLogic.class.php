<?php
/**
 * 用户 logic
 */
namespace User\Logic;
use User\Model\UserModel;			//用户 
class UserLogic extends UserModel
{
	/**
	 * 获取某状态下的全部用户记录
	 * @param  int $state 用户状态 0为冻结 1为正常
	 * @return arrays       以键值作下标 
	 */
	public function getAllListsByState($state = '1')
	{
		$map['state'] = 1;
		$data =  $this->where($map)->select();

		$return = array();
		foreach($data as $value)
		{
			$return[$value[id]] = $value;
		}
		return $return;
	}
	/**快捷方法*/
	public function getAllLists($state = '1')
	{
		return $this->getAllListsByState($state);
	}

	public function getListById($id)
	{
		$map['id'] = (int)$id;
		return $this->where($map)->find();
	}

	public function deleteById($id)
	{
		$id = (int)$id;
		$map['id'] = $id;
		$this->where($map)->delete();
		return true;
	}
}