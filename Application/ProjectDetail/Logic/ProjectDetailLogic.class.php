<?php
/**
 * 项目扩展信息 逻辑
 */
namespace ProjectDetail\Logic;
use ProjectDetail\Model\ProjectDetailModel;
class ProjectDetailLogic extends ProjectDetailModel
{
	/**
	 * 获取某项目的所有设置数值。
	 * @param  [type] $projectId [description]
	 * @return [type]            [description]
	 */
	public function getListsByProjectId($projectId)
	{
		$map['project_id'] = $projectId;
		$data = $this->where($map)->select();

		$return = array();
		foreach($data as $value)
		{
			$return[$value['name']] = $value;
		}
		return $return;
	}

	public function deleteById($id)
	{
		return $this->where("id = $id")->delete();
	}
}