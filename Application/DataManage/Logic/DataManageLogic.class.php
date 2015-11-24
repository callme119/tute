<?php
/**
 * 项目 逻辑
 */
namespace DataManage\Logic;
use Score\Logic\ScoreLogic;										//分值占比
use Project\Logic\ProjectLogic;									//项目分值分布
use ProjectDetail\Logic\ProjectDetailLogic;						//项目扩展信息
use DataModelDetail\Logic\DataModelDetailLogic;					//数据模型扩展信息
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;		//项目类别系数
class DataManageLogic
{
	protected $error = '';
	public function getError()
	{
		return $this->error;
	}
	/**
	 * 获取全部的项目记录.
	 * 1.先取project当前周期，当前类别下的所有信息
	 * 2.查询数据时拼接类别信息，分数信息、工作流信息
	 * 3.扩展项目模型的“扩展信息”，取出需要计算系数的字段。
	 * 4.根据数据模型信息和类别信息、扩展信息，取出用户当前所存储值对应的系数值
	 * 5.取出团队分数时，多个用户的占比的详细信息
	 * 6.计算出多个用户团队占比的总和，并加入新的总和字段后统一返回.
	 * @return array 二维数据
	 */
	public function getAllListsByCycleIdType($cycleId , $type)
	{
		//取周期值
		$cycleId = (int)$cycleId;
		if($cycleId == 0)
		{
			$this->error = "cycleId接收值为0，这是一个错误的请求ID";
			return false;
		}

		//取项目类型
		$type = trim($type);

		//查看是否有缓存
		//todo:
		
		//初始化分值分布表
		$ScoreL = new ScoreLogic();

		//选择条件
		$map['project.cycle_id']			=	$cycleId;
		$map['project_category.type']		=	$type;

		// //
		// $field['score.user_id']						=	'user_id';			//用户ID
		// $field['score.score_percent']				=	'score_percent';	//分值占比
		$field['score.user_id']						=	'user_id';			//用户
		$field['score.score_percent']				=	'score_percent';	//用户分值占比
		$field['project.id'] 						=	'id'; 				//项目ID
		$field['project.title'] 					= 	'title'; 			//项目名称		
		$field['project.user_id'] 					= 	'commit_user_id'; 	//提交用户ID
		$field['project_category.score']			=	'project_category_score';			//项目总分
		$field['project_category.is_team']			=	'is_team';			//是否为团队项目
		$field['project_category.data_model_id']	=	'data_model_id';	//数据模型ID
		$field['project_category.type']				=	'type';				//数据所属类型（科研、育人）
		$field['project_category.id']				=	'category_id';		//
		$field['workflow.state']					=   'state';			//是否审核完成

		//先取项目信息
		$ScoreL->alias("score");
		$projects = $ScoreL->field($field)->join("
			left join __PROJECT__ as project on score.project_id = project.id
			left join __PROJECT_CATEGORY__ as project_category on project.project_category_id = project_category.id
			left join __WORKFLOW__ as workflow on project.id = workflow.project_id
			")->where($map)->select();	
		// echo $ScoreL->getLastSql();
		// echo $ProjectL->getLastSql();	 
		// dump($projects);


		return $projects;
	}
}