<?php
namespace Myjob\Logic;
class MyjobLogic
{
	public function checkUserPermissionInWorkflowLogs($userId,$workflowLogs)
	{
		foreach($workflowLogs as $key => $value)
		{
			$userId == $value[user_id];
			return true;
		}
		return false;
	}
}