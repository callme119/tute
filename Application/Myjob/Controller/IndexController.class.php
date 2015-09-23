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
use Project\Model\ProjectModel; //项目信息表
use Project\Logic\ProjectLogic;        //项目信息
use Chain\Logic\ChainLogic; //审核结点列表逻辑层。
use WorkflowLog\Logic\WorkflowLogLogic; //工作流结点

use Workflow\Service\WorkflowService;
class IndexController extends AdminController{

    public function testAction()
    {
        $WorkflowS = new WorkflowService();
        if(!$WorkflowS->add(5,8,1,10,"这里存的是申请信息"))
        {
            $this->error = $WorkflowS->getError();
            $this->_empty();
        }
    }
    /**
     * 我的工作中待办工作界面
     * 初始化界面
     * 无参数，无返回值
     * 2015年7月16日09:36:08
     */
    
    public function unfinishedAction() {
        //获取USERID
        $userId = get_user_id();

        //获取当前用户下的待办信息
        $map['isClicked'] = '0';
        $this->getMyJobByUserIdIsClickedIsCommitedIsShelved($userId, $map);

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
        $projectId = $workflow[project_id];
        $projectL = new ProjectLogic();
        $project = $projectL->getListById($projectId);

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

        //取审核链信息
        $chainId = $workflow['chain_id'];
        $ChainM = new ChainModel();
        $map = array();
        $map['id'] = $chainId;
        $chain = $ChainM->where($map)->find();
        if($chain == null)
        {
            $this->error = "审核链接点数据取出错误，chainId值为$chainId";
            $this->_empty();
            return false;
        }

        //传值
        $this->assign('doUser',$doUser);
        $this->assign("users",$users);
        $this->assign('showSuggestion',$showSuggestion);
        $this->assign('project',$project);
        $this->assign('workflowLogs',$workflowLogs);
        $this->assign('workflowLog',$workflowLog);
        $this->assign('chain',$chain);
        $this->assign('workflow',$workflow);
        $this->assign('YZBODY',$this->fetch('taskdetail'));
        $this->display(YZTemplate);
    }
    
    /*
     *  保存操作（审批 办结 退回拟稿人 搁置 取消搁置） 
     * */
    public function saveAction()
    {
        //获取用户选择的类型
        $type = I('post.type');
        $workflowLogId = I('post.id');
        if(!is_numeric($type)) 
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
        
        $WorkflowLogL = new WorkflowLogLogic();

        //用户点击 同意 或是 办结。
        if($type == 0)
        {
            if(!$WorkflowLogL->saveCommited($workflowLogId))
            {
                $this->error = $WorkflowLogL->getError();
                $this->_empty();
                return;
            }
        }
       
        //用户选择退回申请人
        elseif($type == 1)
        {
            if(!$WorkflowLogL->backToStart())
            {
                $this->error = $WorkflowLogL->getError();
                $this->_empty();
                return;
            }
        }

        //用户选择搁置
        elseif($type == 2)
        {
            //查询是否为在办。
            $map = array();
            $map['id'] = $workflowLogId;
            $map['is_commited'] = '0';
            if($WorkflowLogM->where($map)->find() == null)
            {
                $this->error = "该流程已办结，不适用搁置操作";
                $this->_empty();
            }

            //更新搁置数据
            $data = array();
            $data['id'] = $workflowLogId;
            $data['is_shelved'] = "1";
            $data['commit'] = I('post.commit');
            $WorkflowLogM->data($data)->save();
        }

        //用户选择取消搁置
        elseif($type == "3")
        {
            //查询是否为待办，且已搁置。
            $map = array();
            $map['id'] = $workflowLogId;
            $map['is_commited'] = '0';
            $map['is_shelved']  = '1';
            if($WorkflowLogM->where($map)->find() == null)
            {
                $this->error = "该流程已办结，不适用搁置操作";
                $this->_empty();
                return false;
            }

            //更新搁置数据
            $data = array();
            $data['id'] = $workflowLogId;
            $data['is_shelved'] = "0";
            $data['commit'] = I('post.commit');
            $WorkflowLogM->data($data)->save();
        }

        //其它
        else
        {
            $this->error = "未接收到正确的变量：process_type";
            $this->_empty();
            return;
        }

        //根据请求来源，给出跳出值。
        $from = I('get.from');
        $p = I('get.p');
        $url = U('Myjob/Index/' . $from . '?p=' . $p);
        $this->success("操作成功" , $url);
    }

    /**
     * 在办工作
     * @return 
     */
    public function  doingAction(){
        $userId = get_user_id();

        //获取当前用户下的在办信息
        $map['isClicked'] = '1';    //已点击
        $map['isCommited'] = '0';   //未提交
        $map['isShelved'] = '0';    //未搁置
        $this->getMyJobByUserIdIsClickedIsCommitedIsShelved($userId, $map);

        $this->assign('YZBODY',$this->fetch('unfinished'));
        $this->display(YZTemplate);
    }
    /**
     * 已办工作
     * @return [type]
     */
    public function  finishedAction(){
        $userId = get_user_id();

        //获取当前用户下的待办信息
        $map['isClicked'] = '1';    //已点击
        $map['isCommited'] = '1';   //已提交
        $this->getMyJobByUserIdIsClickedIsCommitedIsShelved($userId, $map);

        $this->assign('YZBODY',$this->fetch('unfinished'));
        $this->display(YZTemplate);
    }
    /**
     * 搁置工作列表
     * @return [type] [description]
     */
    public function shelvedAction()
    {
        $userId = get_user_id();

        //获取当前用户下的在办信息
        $map['isClicked'] = '1';    //已点击
        $map['isCommited'] = '0';   //未提交
        $map['isShelved'] = '1';    //已搁置
        $this->getMyJobByUserIdIsClickedIsCommitedIsShelved($userId, $map);

        $this->assign('YZBODY',$this->fetch('unfinished'));
        $this->display(YZTemplate);
    }

    /**
     * 通过不同的状态字信息，获取各种工作状态。
     * TODO：如果一条工作被自己审核过两次。比如说，自己是申请人，又是提交人，那么会生成两次。
     * 这个，能弄掉，但是有弄掉还是会有问题。就是分页信息不准了。
     * 而mysql 的DISTINCT 返回值还会有问题。
     * 所以：如果想解决。。还得想办法。。
     * @param  [type] $userId [description]
     * @param  array  $map    [description]
     * @return [type]         [description]
     */
    public function getMyJobByUserIdIsClickedIsCommitedIsShelved($userId , $map = array())
    {

        //取用户基本信息
        $userM = new UserModel();
        $user = $userM->getUserById($userid);

        //获取当前用户的待办工作
        $workflowLogM = new WorkflowLogModel();  
        $workflowLogLists = $workflowLogM->getListsByUserIdIsClickIsCommitedIsShelved($userId,$map);
        $totalCount = $workflowLogM->getTotalCount();

        //去除重复的工作流信息.但这样有个问题，没有办法分页了。暂时去掉
        // $tem = array();
        // foreach($workflowLogLists as $key => $value)
        // {
        //     $tem[$value["workflow_id"]] = $value;
        // }  
        // $workflowLogLists = $tem;

        //获取当前待办工作的工作流数据
        $workflowM = new WorkflowModel();
        $workflowLists = $workflowM->getListsByLists($workflowLogLists , 'workflow_id');

        //获取上一个提交者的用户信息
        $users = $userM->getListsByLists($workflowLists , "subscribe_user_id");

        // //获取项目详细数据
        $ProjectM = new ProjectModel();
        $PublicProjectDetails = $ProjectM->getListsByIds($workflowLists);

        //传值展示
        $this->assign("totalCount",$totalCount);
        $this->assign("workflowLogLists",$workflowLogLists);
        $this->assign("workflowLists",$workflowLists);
        $this->assign('users',$users);
        $this->assign('detail',$PublicProjectDetails);

    }
}