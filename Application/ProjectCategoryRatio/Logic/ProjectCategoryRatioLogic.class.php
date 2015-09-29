<?php
/**
 * 项目类别 系数设置 逻辑
 */
namespace ProjectCategoryRatio\Logic;
use ProjectCategoryRatio\Model\ProjectCategoryRatioModel;
use Project\Model\ProjectModel; 					//项目表
use DataModel\Model\DataModelModel;					//数据模型表
use DataModelDetail\Model\DataModelDetailModel; 	//数据模型详情表
use DataModelDetail\Logic\DataModelDetailLogic;		//数据模型详情
use Project\Logic\ProjectLogic;						//项目信息表 
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;	//项目类别系数表
use ProjectCategory\Logic\ProjectCategoryLogic;		//项目类别 逻辑
use ProjectDetail\Logic\ProjectDetailLogic;			//项目扩展信息
use Score\Logic\ScoreLogic;							//团队开发分值分配
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
	public function getScoreByProjectIdUserId($projectId , $userId)
	{
		$projectScore = $this->getScoreByProjectId($projectId);
			//取分值表
		$ScoreL = new ScoreLogic();
		$score = $ScoreL->getListsByProjectIdUserId($projectId , $userId);

		//通过百分比计算出分数
		$return = (int)ceil($projectScore*$score['score_percent']/100);
		return $return;
	}

	/**
	 * 通过项目ID，取出来项目的总分值
	 * @param  [num] $id [项目ID]
	 * @return [num]     [项目基础分值]
	 */
	public function getScoreByProjectId($projectId)
	{
		//取项目表后缀。
		$projectId = $projectId;

		$projectDetailL = new ProjectDetailLogic();
		$projectDetail = $projectDetailL->getListsByProjectId($projectId);

		$ProjectM = new ProjectModel();
		$project = $ProjectM->where("id = $projectId")->find();
		
		$projectCategoryId = $project['project_category_id'];
		$ProjectCategoryL = new ProjectCategoryLogic();
		$projectCategory = $ProjectCategoryL->getListById($projectCategoryId);
		// dump($projectCategory);

		//取项目模型信息
		$dataModelId = $projectCategory['data_model_id'];
		$DataModelM = new DataModelModel();
		$dataModel = $DataModelM->getListById($dataModelId);
		// dump($dataModel);

		//取出项目模型详情中字段为select的项.
		//同时将项目模型的所有子结点
		$DataModelDetailL = new DataModelDetailLogic();
		$map['type'] = "select";
		$dataModelSelectRoots = $DataModelDetailL->getRootListsByDataModelId($dataModelId , $map);
		$dataModelSons	=	$DataModelDetailL->getSonListsArrayByDataModelId($dataModelId);

		$map['type'] = "money";
		$dataModelMoneyRoots = 	$DataModelDetailL->getRootListsByDataModelId($dataModelId , $map);

		//取项目系数信息
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
		$projectCategoryRatios = $ProjectCategoryRatioL->getListsByProjectCategoryId($projectCategoryId);
		// dump($projectCategoryRatios);

		//取项目扩展信息
		$ProjectDetailL = new ProjectDetailLogic();
		$projectDetails = $ProjectDetailL->getListsByProjectId($projectId);
		// dump($projectDetails);
		// dump($projectCategory);

		//取总分
		$score = $projectCategory['score'];
		// dump($score);
		
		//取selected的系数 先取出name信息 ，再取出name字段对应的选项ID，再取出该ID对应的系数 。
		foreach($dataModelSelectRoots as $root)
		{

			$key = $root['name'];
			//如果存在，证明该系数已经设置，如果不存在，证明项目的系数未被设置。
			//该原因，可能是先有的项目，后来增的数据模型的系数引起的。
			//归避该BUG的方法，可以在修改数据模型前，给出是否有项目类别使用该项目模型的判断。
			if( isset($projectDetails[$key]['value']))
			{
				$value = $projectDetails[$key]['value'];
				$score = $score*$projectCategoryRatios[$value]['ratio']/100;	
			}
		}

		//取money的系数
		// dump($dataModelMoneyRoots);
		foreach($dataModelMoneyRoots as $root)
		{
			$key  = $root['name'];
			$dataModelDetailId = $root['id'];

			//判断是否在项目详情中,存了该项的分值 在项目类型系数设置表中,存了该 数据详情项的系数
			if( isset($projectDetails[$key]['value']) && isset($projectCategoryRatios[$dataModelDetailId]) )
			{
				$value = $projectDetails[$key]['value'];
				$ratio = $projectCategoryRatios[$dataModelDetailId]['ratio'];

				//将百分比转为小数(/100) 将元转化为万元(/10000)
				$score =  $score*$value*$ratio/(100*10000);
			}

		}
		
		//输出(4舍5入)
		return (int)floor($score+0.5);
	}
}