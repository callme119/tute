<?php
/*
 * 我的工作初始化模块
 * creat by 邓浩洋 2015年7月15日14:44:26
 * 2751111108@qq.com
 */

namespace Myjob\Controller;
use Admin\Controller\AdminController;
use Workflow\Model\WorkflowModel; //工作流主表
use Myjob\Model\JobModel;
use WorkflowLog\Model\WorkflowLogModel; //工作流副表
use User\Model\UserModel; //用户表
use Chain\Model\ChainModel; //审核流程具体审核顺序表
use UserDepartmentPost\Model\UserDepartmentPostModel; //用户-部门-岗位表
use Department\Model\DepartmentModel;//部门表
use Post\Model\PostModel;//岗位表
use Myjob\Logic\MyjobLogic; //逻辑层
use DepartmentPost\Model\DepartmentPostModel; //
use PublicProjectDetail\Model\PublicProjectDetailModel; //项目信息表
use Chain\Logic\ChainLogic; //审核结点列表逻辑层。
class IndexController extends AdminController{
    /**
     * 我的工作中待办工作界面
     * 初始化界面
     * 无参数，无返回值
     * 2015年7月16日09:36:08
     */
    
    public function unfinishedAction() {
        $test = new ChainLogic();
        $data = $test->getNextExaminUsersByUserIdAndId(5,24);
        if(!$data)
        {
            echo $test->getError();
        }

        //获取用户ID
        $userId = get_user_id();

        //取用户基本信息
        $userM = new UserModel();
        $user = $userM->getUserById($userid);

        //获取当前用户的待办工作
        $workflowLogM = new WorkflowLogModel();
        $workflowLogLists = $workflowLogM->getTodoListsByUserId($userId);
        // dump($workflowLogLists);

        //获取当前待办工作的工作流数据
        $workflowM = new WorkflowModel();
        $workflowLists = $workflowM->getListsByLists($workflowLogLists , 'workflow_id');
        // dump($workflowLists);

        //获取上一个提交者的用户信息
        $users = $userM->getListsByLists($workflowLists , "subscribe_user_id");
        dump($workflowLists);
        
        //获取项目详细数据
        $PublicProjectDetailM = new PublicProjectDetailModel();
        $PublicProjectDetails = $PublicProjectDetailM->getListsByIds($workflowLists);
       
        var_dump($PublicProjectDetails);
        //传值展示
        $this->assign("workflowLogLists",$workflowLogLists);
        $this->assign("workflowLists",$workflowLists);
        $this->assign('users',$users);
        // $this->assign("projects",$projects);
        $this->assign('YZBODY',$this->fetch('unfinished'));
        $this->display(YZTemplate);
        
    }
    /**
     * 审批详情:
     * 进行项目审批时,触发该atcion.
     * 进行已办工作,在办工作查看时,触发该action
     * @return [type]
     * 3792535@qq.com
     */
    public function  taskDetailAction(){
        $workflowId = I('get.id');
        if(!is_numeric($workflowId) || empty($workflowId)) 
        {
            $this->error("未正确的传入参数,或参数传入错误");
            return;
        }

        //获取当前用户信息
        $userId = get_user_id();

        //取流程基本信息
        $workflowM = new WorkflowModel();
        $workflow = $workflowM->getListById($workflowId);
        dump($workflow);

        // //TODO取流程对应项目的基本信息
        // $projectM = new ProjectModel();
        // $project = getHtmlInfoById($workflow[project_id]);

        //取该流程对应的所有审核意见
        $workflowLogM = new WorkflowLogModel();
        $workflowLogs = $workflowLogM->getListsByWorkflowId($workflow[id]);
        
        //检查权限
        $myjobL = new MyjobLogic();
        if(!$myjobL->checkUserPermissionInWorkflowLogs($userId,$workflowLogs))
        {
            $this->error("对不起，您无此操作权限");
            return;
        }

        //设置已读
        $workflowLogM->setIsClickedById($workflowId);
        
        //取当前流程结点的状态。如果未办，而且是自已是待办，返回true.
        //否则返回false
        $state =  $workflowLogM->getListById($workflowId);
        
        //获取该项目的下一结点审核人员
        $ChainL = new ChainLogic();
        $users = $ChainL->getNextExaminUsersByUserIdAndId($userId , $workflow['chain_id']);
        dump($users);

        //传值
        $this->assign("users",$users);
        $this->assign('project',$project);
        $this->assign('workflowLogs',$workflowLogs);
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