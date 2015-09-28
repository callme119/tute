<?php
/*
 * 后台首页
 */
namespace Admin\Controller;
use Score\Logic\ScoreLogic;		//得分
use Cycle\Logic\CycleLogic;		//周期
use Task\Logic\TaskLogic;		//任务
use BaseTeaching\Logic\BaseTeachingLogic;		//教学工作量
class IndexController extends AdminController
{
    public function indexAction()
    {
    	try
    	{
    		//取用户信息
	    	$userId = get_user_id();

	    	//取当前考核周期
	    	$CycleL = new CycleLogic();
	    	$cycle = $CycleL->getCurrentList();
	    	if($cycle == null)
	    	{
	    		E("系统尚未设置当前周期.请联系系统管理员进行当前周期设置");
	    	}
	    	$cycleId = $cycle['id'];

	    	//取当前周期下 用户教科研得分及预期科研得分
	    	$ScoreL = new ScoreLogic();
	    	$type = "ScientificResearch"; //教科研信息
	    	$ScientificResearchScores = $ScoreL->getScoresByUserIdCycleIdType($userId , $cycleId ,$type);

	    	//取教学工作量
	    	$BaseTeachingL = new BaseTeachingLogic();
	    	$baseTeaching = $BaseTeachingL->getListByUserIdCycleId($userId,$cycleId);

	    	//取服务育人分数
	    	$type = "ServiceEducation";
	    	$serviceEducationScores = $ScoreL->getScoresByUserIdCycleIdType($userId , $cycleId ,$type);

	    	//取当前周期下的 用户教科研总体任务
	    	$TaskL = new TaskLogic();

	    	//取基础科研任务
	    	$type = "BaseScientificResearch";
	    	$baseTask = $TaskL->getListByUserIdCycleIdType($userId,$cycleId , $type);

	    	//取岗位科研任务
	    	$type = "PostScientificResearch";
	    	$postTask = $TaskL->getListByUserIdCycleIdType($userId,$cycleId , $type);
	    	
	    	//求和
	    	$taskValue = $baseTask['value']+$postTask['value'];

	    	//取教学工作量任务
	    	$type = "BaseTeaching";
	    	$baseTeachingTask = $TaskL->getListByUserIdCycleIdType($userId,$cycleId , $type);
	    	

	    	//取服务育人任务
	    	$type = "ServiceEducation";
	    	$serviceEducationTask = $TaskL->getListByUserIdCycleIdType($userId,$cycleId , $type);

	    	$this->assign("baseTeachingTask",$baseTeachingTask['value']);						//教学工作量任务
	    	$this->assign("baseTeachingScore",$baseTeaching['value']);							//教学工作量得分
	    	$this->assign("serviceEducationTask",$serviceEducationTask['value']);				//服务育人任务
	  		$this->assign("ServiceEducationScore",$serviceEducationScores['total_done_score']);	//服务育人得分
	    	$this->assign("totalScore",$ScientificResearchScores['total_score']);
	    	$this->assign("totalDoneScore",$ScientificResearchScores['total_done_score']);
	    	$this->assign("task",$taskValue);
	    	$this->assign('js',$this->fetch('indexJs'));
	    	$this->assign('css',$this->fetch('indexCss'));
	    	$this->assign('YZBODY',$this->fetch());
	    	$this->display(YZTemplate);
    	}
    	catch(\Think\Exception $e)
    	{
    		$this->error = $e;
    		$this->_empty();
    	}
    	
    }
   
}

