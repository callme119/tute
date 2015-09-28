<?php
/**
 * 项目widget
 */
namespace Project\Widget;
use Think\Controller;
use Project\Logic\ProjectLogic;									//项目表
use ProjectCategory\Logic\ProjectCategoryLogic;					//项目类别
use DataModel\Model\DataModelModel;								//数据模型
use DataModel\Logic\DataModelLogic;								//数据模型
use DataModelDetail\Model\DataModelDetailModel;					//数据模型扩展
use ProjectDetail\Logic\ProjectDetailLogic;						//项目详情扩展
use Workflow\Model\WorkflowModel;								//工作流
use WorkflowLog\Model\WorkflowLogModel;							//工作流日志
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;		//类目类别系数
use Score\Logic\ScoreLogic;										//分值分布
use User\Logic\UserLogic;		//用户信息

class ProjectWidget extends Controller
{
	public function getDetailByIdAction($id)
	{
		try
		{
			$id = $projectId = (int)$id;

			//取项目信息
			$ProjectL = new ProjectLogic();
			$project = $ProjectL->getListById($id);
			if($project == null)
			{
				E("project 信息不存在.projectId = $id");
			}

			$project_category_id = $project['project_category_id'];

	        $ProjectCategoryL = new ProjectCategoryLogic();
	        $projectCategory = $ProjectCategoryL->getListById($project_category_id);
	        if($projectCategory == null)
	        {
	        	E("项目类别信息不存在.project_category_id = $project_category_id");
	        }

	        //取项目数据模型信息
	        $DataModelM = new DataModelModel();
	        $dataModelId = $projectCategory['data_model_id'];
	        if( !$dataModel = $DataModelM->where("id = $dataModelId")->find())
	        {
	            E("当前记录选取的数据模型ＩＤ为$dataModelId,但该ＩＤ在数据库中未找到匹配的记录", 1);    
	        }

	        //取数据模型字段信息
	        $DataModelL = new DataModelLogic();
	        $dataModelCommon = $DataModelL->getCommonLists();

	        //取项目模型扩展信息
	        $dataModelDetailM = new DataModelDetailModel();
	        $dataModelDetail = $dataModelDetailM->getListsByDataModelId($dataModelId);

	        //取项目扩展信息
	        $ProjectDetailL = new ProjectDetailLogic();
	        $projectDetail = $ProjectDetailL->getListsByProjectId($projectId);

	        //取审核信息
	        $WorkflowM = new WorkflowModel();
	        $workflow = $WorkflowM->where("project_id = $projectId")->find();

	        //取审核扩展信息
	        $workflowId = $workflow["id"];
	        $WorkflowLogM = new WorkflowLogModel();
	        $workflowLog = $WorkflowLogM->getListsByWorkflowId($workflowId);
	        
	        //取待办信息
	        $todoList = $WorkflowLogM->getTodoListByWorkflowId($workflowId);

	        //取项目总分
	        $ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
	        $sumScore = $ProjectCategoryRatioL->getScoreByProjectId($projectId);
	        
	        //取各用户的占比
	        $ScoreL = new ScoreLogic();
	        $scores = $ScoreL->getAllListsByProjectId($projectId);

	        $this->assign("scores",$scores);
	        $this->assign("sumScore",$sumScore);
	        $this->assign("usersPercent",$usersPercent);
            $this->assign("project",$project);
            $this->assign("workflow",$workflow);
            $this->assign("dataModel",$dataModel);
            $this->assign("dataModelDetail",$dataModelDetail);
            $this->assign("projectDetail",$projectDetail);
            $this->assign("workflowLog",$workflowLog);
            $this->assign("todoList",$todoList);

            $tpl = T("Project@Widget/getDetailById");
           	return $this->fetch($tpl);


		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			return "";
		}
	}

	/**
	 * 通过项目ID，取出来项目的系数
	 * @param  [num] $id [项目ID]
	 * @return [num]     [项目基础分值]
	 */
	public function getScoreByIdUserIdAction($id , $userId)
	{
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
		return $ProjectCategoryRatioL->getScoreByProjectIdUserId($id , $userId);
		
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

	public function getListJoinCagegoryByWorkflowIdAction($workflowId)
	{
		//取项目信息
		$ProjectL = new ProjectLogic();

		$map['w.id'] = $workflowId;

		$field['p.title'] = "name";
		$field['pc.name'] = "project_category_name";
		$field['p.user_id'] = "user_id";

		$ProjectL->alias("p");
		$return = $ProjectL->field($field)->join("left join __PROJECT_CATEGORY__ pc on p.project_category_id = pc.id left join __WORKFLOW__ w on p.id=w.project_id")->where($map)->find();
		// echo $ProjectL->getLastSql();
		return $return;

	}
}