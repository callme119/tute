<?php
/**
 * 任务 WIDGET
 */
namespace Task\Widget;
use Task\Logic\TaskLogic;		//任务设置 
class TaskWidget
{
	public function getValueByUserIdCycleIdTypeAction($userId , $cycleId , $type ,$defaultValue = '-')
	{
		$TaskL = new TaskLogic();
		$task = $TaskL->getListByUserIdCycleIdType($userId,$cycleId,$type);

		return isset($task['value']) ? $task['value'] : $defaultValue;
	}
}