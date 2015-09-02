<?php
/*
 * 我的工作初始化模块
 * creat by 邓浩洋 2015年7月15日14:44:26
 * 2751111108@qq.com
 */

namespace Myjob\Controller;
use Admin\Controller\AdminController;
use Myjob\Model\JobModel;
use WorkflowLog\Model\WorkflowLogModel;
use Workflow\Model\WrokflowModel;
use Project\Model\ProjectModel;
class IndexController extends AdminController{
    /**
     * 我的工作中待办工作界面
     * 初始化界面
     * 无参数，无返回值
     * 2015年7月16日09:36:08
     */
    
    public function indexAction() {
        //获取用户ID
        $userId = get_userId();

        //获取用户is_commit=0的workflow_id
        $workflowLogM = new WorkflowLogModel();
        $workflowIds = $workflowLogM->getWorkflowIdsByUserid($userID);
        
        //获取审核数据
        $workflowM = new WorkflowModel();
        $workflows = $worflowM->getListsByIds($workflowIds , 'workflow_id');

        //获取项目详细数据
        $projectM = new ProjectModel();
        $workflows = $projectM->getListsByIds($workflows,'project_id');
       
        //传值展示
        $this->assign("data",$workflows);
        $this->assign('YZBODY',$this->fetch('index'));
        $this->display(YZTemplate);
        
    }
    /**
     * 工作详情？
     * @return [type]
     */
    public function  taskDetailAction(){
       $this->assign('YZBODY',$this->fetch('taskdetail'));
        $this->display(YZTemplate);
    }
    /**
     * 被审批项目详情
     * @return [type]
     */
     public function  projectDetailAction(){
       $this->assign('YZBODY',$this->fetch('projectdetail'));
        $this->display(YZTemplate);
    }
    
    public function  newExamineAction(){
       $this->assign('YZBODY',$this->fetch('newexamine'));
        $this->display(YZTemplate);
    }
    
    public function  examineListAction(){
       $this->assign('YZBODY',$this->fetch('examinelist'));
        $this->display(YZTemplate);
    }
    /**
     * 在办工作
     * @return 
     */
    public function  doingAction(){
       $this->assign('YZBODY',$this->fetch('doing'));
        $this->display(YZTemplate);
    }
    /**
     * 已办工作
     * @return [type]
     */
    public function  finishedAction(){
       $this->assign('YZBODY',$this->fetch('finished'));
        $this->display(YZTemplate);
    }
    
    public function  unfinishedAction(){
       $this->assign('YZBODY',$this->fetch('unfinished'));
        $this->display(YZTemplate);
    }
    
     public function  finishedProjectDetailAction(){
       $this->assign('YZBODY',$this->fetch('finishedprojectdetail'));
        $this->display(YZTemplate);
    }
    
    public function  finishedTaskDetailAction(){
       $this->assign('YZBODY',$this->fetch('finishedtaskdetail'));
        $this->display(YZTemplate);
    }
    
     public function  doingTaskDetailAction(){
       $this->assign('YZBODY',$this->fetch('doingtaskdetail'));
        $this->display(YZTemplate);
    }
    
    public function  doingProjectDetailAction(){
       $this->assign('YZBODY',$this->fetch('doingprojectdetail'));
        $this->display(YZTemplate);
    }
}