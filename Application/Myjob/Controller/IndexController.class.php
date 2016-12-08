<?php
/*
 * 我的工作初始化模块
 * creat by 邓浩洋 2015年7月15日14:44:26
 * 2751111108@qq.com
*/

namespace Myjob\Controller;
use Admin\Controller\AdminController;
use Workflow\Model\WorkflowModel;                       //工作流主表
use Myjob\Model\JobModel;
use WorkflowLog\Model\WorkflowLogModel;                 //工作流副表
use User\Model\UserModel;                               //用户表
use Chain\Model\ChainModel;                             //审核流程具体审核顺序表
use UserDepartmentPost\Model\UserDepartmentPostModel;   //用户-部门-岗位表
use Department\Model\DepartmentModel;                   //部门表
use Post\Model\PostModel;                               //岗位表
use Myjob\Logic\MyjobLogic;                             //逻辑层
use DepartmentPost\Model\DepartmentPostModel;           //
use Project\Model\ProjectModel;                         //项目信息表
use Project\Logic\ProjectLogic;                         //项目信息
use Chain\Logic\ChainLogic;                             //审核结点列表逻辑层。
use WorkflowLog\Logic\WorkflowLogLogic;                 //工作流结点
use ProjectCategory\Logic\ProjectCategoryLogic;         //项目类别
use DataModel\Model\DataModelModel;                     //数据模型
use DataModel\Logic\DataModelLogic;                     //数据模型
use DataModelDetail\Model\DataModelDetailModel;         //数据模型扩展信息
use ProjectDetail\Logic\ProjectDetailLogic;             //项目扩展数据
use Workflow\Service\WorkflowService;                   //工作流
use Workflow\Logic\WorkflowLogic;                       //工作流
use Score\Logic\ScoreLogic;                             //项目分值分布
use Myjob\Model\Index\FinishedModel;                    //已办工作模型

class IndexController extends AdminController{

    public function backAction()
    {
        $workflowLogId = (int)I('get.id');
        $userId = get_user_id();

        //判断当前流程当前用户是否可以取回
        $FinishedM = new FinishedModel();

        if (!$FinishedM->getIsBack($workflowLogId, $userId))
        {
            $this->error = "当前流程下一审核人已点选，不可取回";
            $this->_empty();
        }

        //取出本审核流程详情信息
        $WorkflowLogL = new WorkflowLogLogic();
        $workflowLog = $WorkflowLogL->getListById($workflowLogId);

        //取出本审核流程的待办信息。
        $currentWorkflowLog = $WorkflowLogL->getCurrentListByWorkflowId($workflowLog[workflow_id]);

        //置本条信息为『待办』
        $WorkflowLogL->setListByIdIsCommitedIsClicked($workflowLogId, 0, 0);

        //删除本审核流程的下一条审核详情
        $WorkflowLogL->deleteById($currentWorkflowLog[id]);

        //更改审核链，为上一结点。
        //取审核信息
        $WorkflowL = new WorkflowLogic();
        $workflow = $WorkflowL->getListById($workflowLog['workflow_id']);

        //更新审核流
        $WorkflowL->setListByIdChainId($workflow['id'], $workflow['pre_chain_id']);

        $this->success("操作成功", U('finished', I('get.')));
    }


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
        if($workflowLog == null)
        {
            E("workfolw log data not find. workflowlogid is $workflowLogId");
        }

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
        if(count($workflowLogs) == 0)
        {
            E("该项目对应的审核流程日志在数据库中未找到。wrokflowId is $workflowId");
        }   

        //取流程基本信息
        $workflowM = new WorkflowModel();
        $workflow = $workflowM->getListById($workflowId);
        if($workflow == null)
        { 
            E("该项目对应的审核流程在数据库中未找到。workflowId is $workflowId");
        }

        // //TODO取流程对应项目的基本信息
        $projectId = $workflow[project_id];
        $projectL = new ProjectLogic();
        $project = $projectL->getListById($projectId);
        if($project == null)
        {
            E("未找到相关项目信息。projectId is $projectId");
        }


        //设置已读
        $WorkflowLogM->setIsClickedById($workflowLogId);
        //设置是否已办,未办则显示审核意见，已办则不需显示 
        if($workflowLog['is_commited'] == '0')
        {
            
            //获取该项目的下一结点审核人员
            $ChainL = new ChainLogic();
            $users = $ChainL->getNextExaminUsersByUserIdAndId($userId , $workflow['chain_id']); 
        }

        //判断是否满足删除条件
        //项目申请人为当前用户　并且项目当前审核流程的当前状态为未办结
        if($project['user_id'] == $userId && $workflowLog['is_commited'] == '0')
        {
            $isDelete = 1;
        }
        else
        {
            $isDelete = 0;
        }

        //取审核链信息
        $chainId = $workflow['chain_id'];
        $ChainM = new ChainModel();
        $map = array();
        $map['id'] = $chainId;
        $chain = $ChainM->where($map)->find();
        if($chain == null)
        {
            $this->error = "审核链接点数据取出错误,该错误可能是删除了已建立了审核流程造成的，chainId值为$chainId";
            $this->_empty();
            return false;
        }

        //传值
        $this->assign('users',$users);
        $this->assign("workflowLog",$workflowLog);
        $this->assign("projectId",$projectId);
        $this->assign('chain',$chain);
        $this->assign("isDelete",$isDelete);
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

        //获取当前用户下的在办信息
        $map['isCommited'] = '1';   //已提交
        $this->getMyJobByUserIdIsClickedIsCommitedIsShelved($userId, $map);

        $Model = new FinishedModel();
        
        $this->assign("Model",$Model);
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

    public function deleteAction()
    {
        try
        {
            $id = (int)I('get.id');
            $userId = get_user_id();

            //判断当前流程为当前用户
            $WorkflowLogL = new WorkflowLogLogic();
            $workflowLog = $WorkflowLogL->getListById($id);
            if($workflowLog == null || $workflowLog['user_id'] != $userId)
            {
                E("该审核流程不存在，或当前审核结点$id的待/在办人$workflowLog[user_id]，并不是当前用户$userId");
            }

            //判断当用流程在当前用户下未提交
            if($workflowLog['is_commited'] == 1)
            {
                E("当前用户$userId并不是当前审核结点$id的待在办人");
            }

            //取审核流信息
            $workflowId = $workflowLog['workflow_id'];
            $WorkflowL = new WorkflowLogic();
            $workflow = $WorkflowL->getListById($workflowId);

            //当前项目的审核结点，是否为根结点
            $ChainL = new ChainLogic();
            $chain = $ChainL->getListById($workflow[chain_id]);
            if ($chain[pre_id] != 0)
            {
                $this->error = "错误：当前结点非起始结点。";
                $this->_empty();
                return;
            }
            
            //取项目信息
            $projectId = $workflow['project_id'];
            $ProjectL = new ProjectLogic();
            $project = $ProjectL->getListById($projectId);

            //判断申请人为当前用户
            if($project['user_id'] != $userId)
            {
                E("当前项目的申请人$project[user_id]非当前用户$userId");
            }

            //删除项目基本信息
            $ProjectL->deleteById($projectId);

            //删除项目扩展数据
            $ProjectDetailL = new ProjectDetailLogic();
            $ProjectDetailL->deleteByProjectId($projectId);

            //删除项目分数分布信息
            $ScoreL = new ScoreLogic();
            $ScoreL->deleteByProjectId($projectId);

            //删除所有审核日志
            $WorkflowL->deleteByProjectId($projectId);
            
            //删除审核链信息
            $WorkflowLogL->deleteByWorkflowId($workflowId);

            //根据请求来源，给出跳出值。
            $from = I('get.from');

            $url = U('Myjob/Index/' . $from . '?id=' , I('get.'));
            $this->success("操作成功" , $url);

        }
        catch(\Think\Exception $e)
        {
            $this->error = $e;
            $this->_empty();
        }
        
    }
}