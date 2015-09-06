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
use Chain\Logic\ChainLogic;
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
        if(!is_string($workflowId) || empty($workflowId)) 
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

        // //取流程对应项目的基本信息
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
        dump($workflowLogs);
        
        //取下一审核结点的岗位信息
        $chainM = new ChainModel();
        $nextPostList = $chainM->getNextListById($workflow[chain_id]);
        $nextPost = is_array($nextPostList)?$nextPostList[now_post]:null;
        $nowPostList = $chainM->getListById($workflow[chain_id]);
        $nowPost = $nowPostList[now_post];

        dump($nextPostList);
        dump($nowPostList);
        dump($nextPost);
        dump($nowPost);
        //取当前用户当前岗位的当前部门
        $userDepartmentPostM = new UserDepartmentPostModel();
        $currentDepartments = $userDepartmentPostM->getListsByUseridAndPost($userId , $nowPost);

        //返回值为false，则说明在上一步的审核中，未对chain_id进行变更。
        //请检查SAVEACTON中，对chain的操作情况
        if(!$currentDepartments)
        {
            $this->error("系统错误，该错误可能是岗位变动引起的，请联系管理员，错误代码：123");
            return;
        }
        dump($currentDepartments);
        //获取上级部门列表树(返回值去除树的样子,为平行数组)
        //todo:如果用户处于两个部门的两个岗位时，需要重新考虑。
        $departmentM = new DepartmentModel();
        $parentDepartmentsTreeArray = $departmentM->getParentTreeByLists($currentDepartments , "department_id");
        dump($parentDepartmentsTreeArray);

        //取部门列表下，存在的下一审核岗位信息。
        $DepartmentPostM = new DepartmentPostModel();
        $departmentPosts = $DepartmentPostM->getListByDepartmentsAndPost($parentDepartmentsTreeArray,$nextPost);
        dump($departmentPosts);

        //取当前部门、岗位下的人员，有则直接返回第一个岗位部门下的列表。
        $users = $userDepartmentPostM->getFirstListsByDepartmentPostIds($departmentPosts);

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