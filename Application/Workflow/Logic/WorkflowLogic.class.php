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
		$map[project_id] = (int)$projectId;
		$list = $this->where($map)->find();
		if ($list === null)
		{
			$this->error = "未查询到projectId为$projectId的信息";
			return false;
		}
		return $list;
	}
	public function deleteByProjectId($projectId)
	{
		$map['project_id'] = (int)$projectId;
		$data = $this->where($map)->delete();
		return $data;
	}
	

	public function getListById($id)
	{
		$map['id'] = (int)$id;
		$data = $this->where($map)->find();
		return $data;
	}
}