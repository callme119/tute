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
use Score\Logic\ScoreLogic;							//用户分值表
use Think\Controller;
class ProjectCategoryRatioWidget extends Controller
{
	/**
	 * 通过项目ID，取出来项目的系数
	 * @param  [num] $id [项目ID]
	 * @return [num]     [项目基础分值]
	 */
	public function getScoreByProjectIdAction($projectId ,$userId)
	{
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
		$score = $ProjectCategoryRatioL->getScoreByProjectId($projectId);

		//取分值表
		$ScoreL = new ScoreLogic();
		$scores = $ScoreL->getListsByPorjectId($projectId);

		//统计当前用户及总共的百分比。
		$userPercent = 0;
		$sumPercent = 0;
		foreach($scores as $value)
		{
			$sum += $value['score_percent'];
			if($value['user_id'] == $userId)
			{
				$userPercent = $value['score_percent'];
			}
		}

		//通过百分比计算出分数
		$score = (int)ceil($score*$userPercent/$sumPercent);

		//输出
		return $score;
	}
}