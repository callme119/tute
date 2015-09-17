<?php
/**
 * 和用户表进行链接的 用户基础工作量 表
 */
namespace BaseTeaching\Logic;
use User\Model\UserModel;		//用户表
use BaseTeaching\Model\BaseTeachingModel;		//基础工作量表
use Cycle\Logic\CycleLogic;		//周期逻辑表
class UserLogic extends UserModel
{
	/**
	 * 获取全部正常用户的信息
	 * @param  integer $state 1为正常用户，0为冻结用户
	 * @return array          二维数组
	 */
	public function getAllLists($state = 1)
	{
		$this->alias("a");
		$field['a.id'] = 'id';
		$field['a.name'] = 'name';
		$field['b.cycle_id'] = 'cycle_id';
		$field['b.value'] = 'value';
		$field['b.remark'] = 'remark';
		$map['a.state'] = $state;
		return $this->where($map)->field($field)->join("left join __BASE_TEACHING__ b on a.id = b.user_id")->select();
	}

	/**
	 * 获取全部正常用户某周期的所有信息
	 * @param  integer $state 1为正常用户，0为冻结用户
	 * @return array          二维数组
	 */
	public function getAllListsByCycleId($cycleId , $state = 1)
	{
		//查是否有cycleID的记录
		$CycleL = new CycleLogic();
		if(!$CycleL->getListById($cycleId))
		{
			$this->error = $CycleL->getError();
			return false;
		}

		$this->alias("a");

		$field['a.id'] = 'id';
		$field['a.name'] = 'name';
		$field['b.cycle_id'] = 'cycle_id';
		$field['b.value'] = 'value';
		$field['b.remark'] = 'remark';

		$map['a.state'] = $state;
		$map['b.cycle_id']	= $cycleId;

		$data =  $this->where($map)->field($field)->join("left join __BASE_TEACHING__ b on a.id = b.user_id")->select();
		$return = array();
		foreach($data as  $value)
		{
			$return[$value['id']] = $value;
		}
		return $return;
	}
}