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
		$by = (I('get.by') == "") ?  "desc" : I('get.by');
		$order = "b.value " . $by;
		$field['a.id'] 			= 'id'; //用户ID
		$field['a.name'] 		= 'name';//用户姓名
		$field['b.id']			= 'task_id';	//任务ID
		$field['b.cycle_id'] 	= 'cycle_id';//周期ID
		$field['b.value']		= 'value';//任务值
			
		//取总数
		$map = array();
		$map['a.state'] = 1;
		$map['b.cycle_id'] = $cycleId;
		$map['b.type'] = CONTROLLER_NAME;
		$this->totalCount = $this->alias('a')->where($map)->join("left join __TASK__ b on a.id=b.user_id")->count();

   		//取任务信息
		$map = array();
		$map['a.state'] 		= 1;
		$map['b.cycle_id'] = $cycleId;
		$map['b.type'] = CONTROLLER_NAME;
		$return = $this->field($field)->alias('a')->order($order)->where($map)->join("left join __TASK__ b on a.id=b.user_id")->page($this->p,$this->pageSize)->select();
		return $return;
	}
}