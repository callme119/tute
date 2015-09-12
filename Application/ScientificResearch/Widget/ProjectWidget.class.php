<?php
/**
 * 项目WIDGET
 */
namespace ScientificResearch\Widget;
use Project\Model\ProjectModel; 					//项目表
use DataModel\Model\DataModelModel;					//数据模型表
use DataModelDetail\Model\DataModelDetailModel; 	//数据模型详情表
use DataModelDetail\Logic\DataModelDetailLogic;		//数据模型详情
use Project\Logic\ProjectLogic;					//项目信息表 
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;	//项目类别系数表
use ProjectCategory\Logic\ProjectCategoryLogic;		//项目类别 逻辑
use ProjectDetail\Logic\ProjectDetailLogic;			//项目扩展信息
use Think\Controller;
class ProjectWidget extends Controller
{
	/**
	 * 通过项目ID，取出来项目的系数
	 * @param  [num] $id [项目ID]
	 * @return [num]     [项目基础分值]
	 */
	public function getRatioByIdAction($id)
	{
		//取项目表后缀。
		$projectId = $id;
		$ProjectM = new ProjectModel();
		$project = $ProjectM->where("id = $id")->find();
		// dump($project);

		//取项目模型信息
		$dataModelId = $project['data_model_id'];
		$DataModelM = new DataModelModel();
		$dataModel = $DataModelM->getListById($dataModelId);
		// dump($dataModel);

		//取出项目模型详情中字段为select的项.
		//同时将项目模型的所有子结点
		$DataModelDetailL = new DataModelDetailLogic();
		$map['html_type'] = "select";
		$dataModelSelectRoots = 	$DataModelDetailL->getRootListsByDataModelId($dataModelId , $map);
		$dataModelSons	=	$DataModelDetailL->getSonListsArrayByDataModelId($dataModelId);

		//取项目对应的 项目分类 信息
		$projectCategoryId = $project['project_category_id'];

		$ProjectCategoryL = new ProjectCategoryLogic();
		$projectCategory = $ProjectCategoryL->getListById($projectCategoryId);

		//取项目系数信息
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
		$projectCategoryRatios = $ProjectCategoryRatioL->getListsByProjectCategoryId($projectCategoryId);

		//取项目扩展信息
		$ProjectDetailL = new ProjectDetailLogic();
		$projectDetails = $ProjectDetailL->getListsByProjectId($projectId);
		// dump($projectDetails);
		// dump($projectCategory);

		//取总分
		$score = $projectCategory['score'];

		//先取出name信息 ，再取出name字段对应的选项ID，再取出该ID对应的系数 。
		foreach($dataModelSelectRoots as $root)
		{
			$key = $root['name'];
			$value = $projectDetails[$key]['value'];
			$score =  floor($score*$projectCategoryRatios[$value]['ratio']/100);
		}

		//输出
		return $score;
	}
}