<?php
/**
 * 工作流 逻辑
 */
namespace Workflow\Logic;
use Workflow\Model\WorkflowModel;
class WorkflowLogic extends WorkflowModel
{
	public function getListByProjectId($projectId)
	{
		$projectId = (int)$projectId;
		$map['project_id'] = $projectId;
		return $this->where($map)->find();
	}
}