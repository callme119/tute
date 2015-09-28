<?php
namespace BaseTeaching\Widget;
use BaseTeaching\Logic\BaseTeachingLogic;		//教学工作量
class BaseTeachingWidget
{
	public function getValueByUserIdCycleIdAction($userId , $cycleId)
	{
		//取基本信息
		$BaseTeachingL = new BaseTeachingLogic();
		$baseTeaching = $BaseTeachingL->getListByUserIdCycleId($userId,$cycleId);

		return $baseTeaching['value'];
	}
}