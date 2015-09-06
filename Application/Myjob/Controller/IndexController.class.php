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
        dump($workflowLogLists);

        //获取当前待办工作的工作流数据
        $workflowM = new WorkflowModel();
        $workflowLists = $workflowM->getListsByLists($workflowLogLists , 'workflow_id');

        //获取上一个提交者的用户信息
        $users = $userM->getListsByLists($workflowLists , "subscribe_user_id");

        //获取项目详细数据
        $PublicProjectDetailM = new PublicProjectDetailModel();
        $PublicProjectDetails = $PublicProjectDetailM->getListsByIds($workflowLists);

        //传值展示
        $this->assign("workflowLogLists",$workflowLogLists);
        $this->assign("workflowLists",$workflowLists);
        $this->assign('users',$users);
        $this->assign('detail',$PublicProjectDetails);
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
        $workflowLogId = I('get.id');
        if(!is_numeric($workflowLogId) || empty($workflowLogId)) 
        {
            $this->_empty();
            return;
        }

        //取流程基本信息
        $WorkflowLogM = new WorkflowLogModel();
        $workflowLog = $WorkflowLogM->getListById($workflowLogId);

        //获取当前用户信息
        $userId = get_user_id();

        //判断当前结点是否属于用户
        if($workflowLog['user_id'] != $userId)
        {
            $this->error = "当前结点并不属于当前用户";
            $this->_empty();
            return;
        }

        //取流程ID
        $workflowId = $workflowLog["workflow_id"];

        //取该流程对应的所有审核意见
        $workflowLogs = $WorkflowLogM->getListsByWorkflowId($workflowId);
        
        //取流程基本信息
        $workflowM = new WorkflowModel();
        $workflow = $workflowM->getListById($workflowId);

        // //TODO取流程对应项目的基本信息
        // $projectM = new ProjectModel();
        // $project = getHtmlInfoById($workflow[project_id]);

        //设置已读
        $WorkflowLogM->setIsClickedById($workflowLogId);
        //设置是否已办,未办则显示审核意见，已办则不需显示 
        if($workflowLog['is_commited'] == '0')
        {
            
            //获取该项目的下一结点审核人员
            $ChainL = new ChainLogic();

            $users = $ChainL->getNextExaminUsersByUserIdAndId($userId , $workflow['chain_id']); 
        }
        else
        {
            //取在/待办人信息
            $doUser = $WorkflowLogM->getListByWorkflowIdAndIsCommit($workflowId , '0');
        }

        //传值
        $this->assing("error",$this->error);
        $this->assign('doUser',$doUser);
        $this->assign("users",$users);
        $this->assign('showSuggestion',$showSuggestion);
        $this->assign('project',$project);
        $this->assign('workflowLog',$workflowLog);
        $this->assign('workflow',$workflow);
        $this->assign('YZBODY',$this->fetch('taskdetail'));
        $this->display(YZTemplate);
    }

    public function saveAction()
    {
        //获取用户选择的类型
        $type = I('post.process_type');
        $workflowLogId = I('post.id');
        if(empty($type) || !is_numeric($type) || empty($id) || !is_numeric($id))
        {
            $this->error = "未接收到正确的变量：process_type或id";
            $this->_empty();
            return;
        }

        //获取当前用户
        $userId = get_user_id();

        //获取当前审核结点信息
        $WorkflowLogM = new WorkflowLogModel();
        $workflowLog = $WorkflowLogM->getListById($workflowLogId);

        //进行权限判断，即用户现在是否有权限对该审核结点进行操作
        if($workflowLog['user_id'] != $userId)
        {
            $this->error = "对不起，无此操作权限";
            $this->_empty();
            return;
        }
        
        //判断流程状态
        if($workflowLog['is_commited'] == '1' || $workflowLog['is_shelved'] == '1')
        {
            $this->error = "对不起，该流程结点已审核或已被搁置";
            $this->_empty();
            return;
        }
    
        
        //进行下一步审核操作处理
        if($type == 0)
        {
            //改变前结审核结点信息。
            //判断是否最后结点
            //最后结点，改变当前流程信息
        }

        //用户选择退回申请人
        elseif($type == 1)
        {

        }

        //用户选择搁置
        elseif($type == 2)
        {

        }

        else
        {
            $this->error = "未接收到正确的变量：process_type";
            $this->_empty();
            return;
        }

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