<?php
/**
 * 工作流 ，服务接口
 * panjie 
 * 3792535@qq.com
 * */
 namespace Workflow\Service;
 use Examine\Model\ExamineModel;	//审核流程表
 use Chain\Model\ChainModel;		//审核流程链表
 use Workflow\Model\WorkflowModel;	//工作流表
 use WorkflowLog\Model\WorkflowLogModel;	//工作流结点表
 use UserDepartmentPost\Model\UserDepartmentPostModel;	//用户部门岗位表
 use Chain\Logic\ChainLogic;

 class WorkflowService
 {
 	protected $error;

 	public function __set($name , $value) {
 		$this->$name = $value;
        return ;
    }
 	 /**
 	  * 
     * 获取数据对象的值
     * @access public
     * @param string $name 名称
     * @return mixed
     */
    public function __get($name) {
        return isset($this->$name)?$this->$name:null;
    }

    /**
     * 定义统一的错误返回信息
     * @return string 内部发生错误
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * 添加新的工作流 新的工作流被用户点“提交时触发”
     * @param num $userId                申请人\拟搞人
     * @param number $examineId             审核流程ID
     * @param num $projectId 项目ID
     * @param num $checkUserId           用户选择的工作流审核人员
     * @param bool $isSelf            将下一审核人置为自己，true .
     */
    public function add($userId , $examineId , $projectId, $checkUserId , $commit = "申请", $isSelf = false)
    {
    	//取审核流程信息
    	$ExamineM = new ExamineModel();
    	$map = array();
    	$map['id'] = $examineId;
    	$examine = $ExamineM->where($map)->find();
    	if(!$examine)
    	{
    		$this->error = "传入examineid值为'$examineId'有误，不存在该审核流程记录";
    		return false;
    	}

    	//取开始岗位信息
    	$chainId = $examine['chain_id'];
    	$ChainM = new ChainModel();
    	$map = array();
    	$map['id'] = $chainId;
    	$chain = $ChainM->where($map)->find();

    	//判断申请人是否具有该岗位
    	$postId = $chain['now_post'];
    	$UserDepartmentPostM = new UserDepartmentPostModel();
    	$userDepartmentPost = $UserDepartmentPostM->getPostListByUserIdPostId($userId,$postId);
    	if(count($userDepartmentPost) == 0)
    	{
    		$this->error = "该用户选择的审核流程不在其可选范围内";
    		return false;
    	}

    	//取当前用户当前结点的所有审核用户列表
    	$ChainL = new ChainLogic();
    	$userLists = $ChainL->getNextExaminUsersByUserIdAndId($userId , $chainId);


    	//判断传入 审核用户 是否处于列表中
    	if(!isset($userLists[$checkUserId]))
    	{
    		$this->error = "提交审核人员信息有误，该审核人员不属于该用户对应的该流程";
    		return false;
    	}

    	//添加工作流数据
    	$data = array();
    	$data['examine_id'] = $examineId;
        if ($isSelf)
        {
            $data['chain_id']   = $chain[id]; //用户的选择仅是保存，那么，本节点 
        }
        else
        {
            $data['chain_id']    = $chain['next_id']; //既然用户已经提交了，那好。就应该是下一结点。 
        }
    	
    	$data['project_id'] 	= $projectId;
    	$WorkflowM = new WorkflowModel();
    	if(!$WorkflowM->create($data))
    	{
    		$this->error = $WorkflowM->getError();
    		return false;
    	}
    	$workflowId = $WorkflowM->add();

    	//存工作流结点表
        //当前用户增加已办/在办信息信息
        $data   = array();
        $data['workflow_id']    = $workflowId;
        $data['pre_id'] = '0';
        $data['user_id'] = $userId;
        $data['is_clicked'] = "1";
        if ($isSelf)
        {
            
            $data['is_commited'] = "0"; 
        }
        else
        {
            $data['is_commited'] = "1"; 
        }
        
        $data['commit'] = $commit;
        $WorkflowLogM = new WorkflowLogModel();
        if(!$WorkflowLogM->create($data))
        {
            $this->error = $WorkflowLogM->getError();
            $this->error .= "工作流表数据已经写进去了……，郁闷的，就这样吧。";
            return false;
        }
        $preId = $WorkflowLogM->add();

        //如果用户选择的是保存，而非提交
        if (!$isSelf)
        {
            //下一用户增加待办信息
            $data   = array();
            $data['workflow_id']    = $workflowId;
            $data['pre_id'] = $preId;
            $data['user_id'] = $checkUserId;
            $WorkflowLogM = new WorkflowLogModel();
            if(!$WorkflowLogM->create($data))
            {
                $this->error = $WorkflowLogM->getError();
                $this->error .= "工作流表数据已经写进去了……，郁闷的，就这样吧。";
                return false;
            }
            $WorkflowLogM->add();
        }
        

    	return true;
    }

 }