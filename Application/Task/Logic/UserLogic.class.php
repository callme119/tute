<?php
/**
 * 调用用户信息
 */
namespace Task\Logic;
use User\Model\UserModel;	//用户
class UserLogic extends UserModel
{
	public function getJoinUserListsByCycleId($cycleId)
	{
		$by = empty(I('get.by')) ? I('get.by') : "desc";
		$order = "b.value " . $by;
		$field['a.id'] 			= 'id'; //用户ID
		$field['a.name'] 		= 'name';//用户姓名
		$field['b.id']			= 'task_id';	//任务ID
		$field['b.cycle_id'] 	= 'cycle_id';//周期ID
		$field['b.value']		= 'value';//任务值
			
		//取总数
		$map = array();
		$map['state'] = 1;
		$map['cycle_id'] = $cycleId;
		$this->totalCount = $this->where($map)->count();

		//取任务信息
		$map = array();
		$map['a.state'] 		= 1;
		$map['b.cycle_id'] = $cycleId;
		$return = $this->field($field)->alias('a')->order($order)->where($map)->join("left join __TASK__ b on a.id=b.user_id")->page($this->p,$this->pageSize)->select();
		return $return;
	}
}