<?php
/**
 * 任务widget
 */
namespace TeachingLoad\Widget;
use Task\Widget\TaskWidget as TaskW;		//任务
class TaskWidget extends TaskW
{
	public function getValueByUserIdCycleIdAction($userId , $cycleId )
	{
		$type = "BaseTeaching";
		return $this->getValueByUserIdCycleIdTypeAction($userId , $cycleId , $type);
	}
	
}