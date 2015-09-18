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

	/**
	 * 删除某个项目类别相关的所有信息
	 * @param  int $projectCategoryId 项目类别ID
	 * @return                     [description]
	 */
	public function deleteListsByProjectCategoryId($projectCategoryId)
	{
		$map['project_category_id'] = $projectCategoryId;
		$this->where($map)->delete();
	}

	/**
	 * 返回指定 项目类别ID的信息
	 * @param  int $projectCategoryId 项目类别ID
	 * @return array                     多维数组
	 */
	public function getListsByProjectCategoryId($projectCategoryId = 0)
	{
		$map['project_category_id'] = $projectCategoryId;
		$data = $this->where($map)->select();
		$return = array();
		foreach($data as $value)
		{
			$return[$value['data_model_detail_id']] = $value;
		}
		return $return;
	}

	public function getListByProjectCategoryIdDataModelDetailId($projectCategoryId , $dataModelDetailId)
	{
		$map['project_category_id'] = (int)$projectCategoryId;
		$map['data_model_detail_id'] = (int)$dataModelDetailId;
		return $this->where($map)->find();
	}
}