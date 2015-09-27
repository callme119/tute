<?php
/**
 * 项目WIDGET
 */
namespace ScientificResearch\Widget;
use ProjectCategoryRatio\Widget\ProjectCategoryRatioWidget;
class ProjectWidget extends ProjectCategoryRatioWidget
{
	/**
	 * 通过项目ID，取出来项目的系数
	 * @param  [num] $id [项目ID]
	 * @return [num]     [项目基础分值]
	 */
	public function getScoreByIdUserIdAction($id , $userId)
	{
		return $this->getScoreByProjectIdUserIdAction($id , $userId);
	}

	/**
	 * 通过项目ID，取出来项目的系数
	 * @param  [num] $id [项目ID]
	 * @return [num]     [项目基础分值]
	 */
	public function getScoreByIdAction($id , $userId)
	{
		return $this->getScoreByProjectIdAction($id);
	}
}