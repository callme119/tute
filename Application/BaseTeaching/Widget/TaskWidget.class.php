<?php
/**
 * 任务 WIDGET
 */
namespace BaseTeaching\Widget;
use Task\Logic\TaskLogic;		//任务设置 
use Think\Controller;
class TaskWidget extends Controller
{
	public function getListByUserIdCycleIdAction($userId , $cycleId , $defaultValue = '-')
	{
		$TaskL = new TaskLogic();
		$type = "BaseTeaching";
		$task = $TaskL->getListByUserIdCycleIdType($userId,$cycleId,$type);

		echo isset($task['value']) ? $task['value'] : $defaultValue;
	}
}