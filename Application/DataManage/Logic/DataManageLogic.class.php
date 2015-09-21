<?php
/**
 * 项目 逻辑
 */
namespace DataManage\Logic;
use Score\Logic\ScoreLogic;			//分值占比
use Project\Logic\ProjectLogic;		//项目逻辑
use ProjectDetail\Logic\ProjectDetailLogic;		//项目扩展信息
use DataModelDetail\Logic\DataModelDetailLogic;		//数据模型扩展信息
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
		
		//初始化项目
		$ProjectL = new ProjectLogic();
		$ProjectL->alias("project");

		//
		$map['project.cycle_id']			=	$cycleId;
		$map['project_category.type']		=	$type;

		// //
		// $field['score.user_id']						=	'user_id';			//用户ID
		// $field['score.score_percent']				=	'score_percent';	//分值占比
		$field['project.id'] 						=	'id'; 		//项目ID
		$field['project.title'] 					= 	'title'; 	//项目名称		
		$field['project.user_id'] 					= 	'commit_user_id'; 	//提交用户ID
		$field['project_category.score']			=	'score';			//项目总分
		$field['project_category.is_team']			=	'is_team';			//是否为团队项目
		$field['project_category.data_model_id']	=	'data_model_id';	//数据模型ID
		$field['project_category.type']				=	'type';				//数据所属类型（科研、育人）
		$field['project_category.id']				=	'category_id';		//
		$field['workflow.state']					=   'state';		//是否审核完成

		//先取项目信息
		$projects = $ProjectL->field($field)->join("
			left join __PROJECT_CATEGORY__ as project_category on project.project_category_id = project_category.id
			left join __WORKFLOW__ as workflow on project.id = workflow.project_id
			")->where($map)->select();	
		// echo $ProjectL->getLastSql();	 
		// dump($projects);

		$ProjectDetailL = new ProjectDetailLogic();		//项目扩展信息
		$DataModelDetailL = new DataModelDetailLogic();	//数据模型扩展信息
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();//项目类别系数
		$ScoreL = new ScoreLogic();					//初始化分值占比表

		//取所有涉及到项目模型的可以设置系数的信息
		$roots = array();
		$tempArray = array(); //换KEY的临时文件
		$totalScores = array();	//分数表
		foreach($projects as $key => $project)
		{
			//取出项目ID及项目模型ID
			$projectId = $project['id'];
			$dataModelId = $project['data_model_id'];

			//取出需要计算的系数字段，相关系数对应的ID
			$type = 'select';
			if(!isset($roots[$dataModelId]))
			{
				$roots[$dataModelId] = $DataModelDetailL->getRootListsByDataModelIdType($dataModelId,$type);
			}

			//取项目扩展信息,取出系数进行乘法运算。
			$ratios = 100;
			foreach($roots[$dataModelId] as $root)
			{
				$name = $root['name'];

				//根据NAME值，去项目扩展数据库里取数
				$projectDetail = $ProjectDetailL->getListByProjectIdName($projectId , $name);
				$dataModelDetailId = $projectDetail['value'];

				//取出该VALUE在系数表中的系数值
				$projetcCategoryId = $project['category_id'];
				$projectCategoryRatio = $ProjectCategoryRatioL->getListByProjectCategoryIdDataModelDetailId($projetcCategoryId,$dataModelDetailId);
				// dump($projectCategoryRatio);
				$ratios *= $projectCategoryRatio['ratio']/100;
				$ratios = (int)ceil($ratios);
			}

			//设置总系数
			$project['ratios'] = $ratios;	

			//取当前项目下的团队用户信息
			$scores  = $ScoreL->getAllListsByProjectId($projectId);
			
			//$sumPercent = $ScoreL->getSumPercentByProjectId($projectId);
			//计算分值占比总和
			$sumPercent = 0;
			foreach($scores as $key => $score)
			{
				$sumPercent += (int)$score['score_percent'];
			}

			//添加分值占比总和
			foreach($scores as $key => $score)
			{
				$score['sum_percent'] = $sumPercent;
				$scores[$key] = array_merge($score,$project);
			}
			// echo "接拼前";
			// dump($scores);
			$totalScores = array_merge($totalScores , $scores);
			// echo "接拼后";
			// dump($totalScores);	
		}
		//加入缓存TODO:
		return $totalScores;
	}


}