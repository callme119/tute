<?php
/**
 * 项目WIDGET
 */
namespace ProjectCategoryRatio\Widget;
use Project\Model\ProjectModel; 					//项目表
use DataModel\Model\DataModelModel;					//数据模型表
use DataModelDetail\Model\DataModelDetailModel; 	//数据模型详情表
use DataModelDetail\Logic\DataModelDetailLogic;		//数据模型详情
use Project\Logic\ProjectLogic;					//项目信息表 
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;	//项目类别系数表
use ProjectCategory\Logic\ProjectCategoryLogic;		//项目类别 逻辑
use ProjectDetail\Logic\ProjectDetailLogic;			//项目扩展信息
use Think\Controller;
class ProjectCategoryRatioWidget extends Controller
{
	/**
	 * 通过项目ID，取出来项目的系数
	 * @param  [num] $id [项目ID]
	 * @return [num]     [项目基础分值]
	 */
	public function getScoreByProjectIdAction($projectId)
	{
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
		$score = $ProjectCategoryRatioL->getScoreByProjectId($projectId);
		//输出
		return $score;
	}
}