<?php
/**
 * 工作流 逻辑
 */
namespace Workflow\Logic;
use Workflow\Model\WorkflowModel;
class WorkflowLogic extends WorkflowModel
{

	public function deleteByProjectId($projectId)
	{
		$map['project_id'] = (int)$projectId;
		$data = $this->where($map)->delete();
		return $data;
	}
	
	public function getListByProjectId($projectId)
	{
		$projectId = (int)$projectId;
		$map['project_id'] = $projectId;
		return $this->where($map)->find();
	}

	public function getListById($id)
	{
		$map['id'] = (int)$id;
		$data = $this->where($map)->find();
		return $data;
	}
}