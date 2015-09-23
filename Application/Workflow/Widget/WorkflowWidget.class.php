<?php
/**
 * 工作流基本信息 widget 
 */
namespace Workflow\Widget;
use Workflow\Logic\WorkflowLogic;			//工作流逻辑
use Think\Controller;
class WorkflowWidget extends Controller
{
	public function getStateByProjectIdAction($projectId , $unfinished = "审核中" , $finished = "已审核")
	{
		$projectId = (int)$projectId;

		//取当前数据
		$WorkflowL = new WorkflowLogic();
		if(!$workflow = $WorkflowL->getListByProjectId($projectId))
		{
			return;
		}

		//按不同状态给值
		if($workflow['is_finished'] == 0)
		{
			return $unfinished;
		}
		else
		{
			return $finished;
		}

	}
}