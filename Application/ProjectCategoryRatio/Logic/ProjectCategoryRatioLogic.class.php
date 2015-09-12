<?php
/**
 * 项目类别 系数设置 逻辑
 */
namespace ProjectCategoryRatio\Logic;
use ProjectCategoryRatio\Model\ProjectCategoryRatioModel;
class ProjectCategoryRatioLogic extends ProjectCategoryRatioModel
{
	/**
	 * 将项目类别中的参数设置信息添加到表中
	 * @param int $projectCategoryId 项目类别ID
	 * @param array $dataModelDetails  数据模型扩展IDS
	 */
	public function addListsByProjectCategoryIdDataModelDetailIds($projectCategoryId , $dataModelDetails)
	{	
		foreach($dataModelDetails as $key=>$dataModelDetail)
		{
			$data = array();
			$data = array(
				'project_category_id'	=>	$projectCategoryId , 
				'data_model_detail_id'	=>	$key,
				'ratio'					=> 	$dataModelDetail,
				);
			if($this->create($data))
			{
				$this->add();
			}

		}
	}
}