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

		//利用表后缀，取出该项目的具体信息
		$tableSuffix = $dataModel['suffix'];
		$ProjectL = new ProjectLogic();
		$ProjectL->setTableSuffix($tableSuffix);
		$projectDetailId = $project['project_detail_id'];
		$projectDetailSuffix = $ProjectL->getListByIdFromSuffixTable($projectDetailId);

		// dump($projectDetailSuffix);
		//取出项目模型中的值,并计算
		$score = $dataModel[score];
		foreach($dataModelSelectRoots as $root)
		{
			$name = $root['name'];
			$key = $projectDetailSuffix[$name];
			$score =  floor($score*$dataModelSons[$key]['ratio']/100);
		}

		//输出
		echo $score;
	}
}