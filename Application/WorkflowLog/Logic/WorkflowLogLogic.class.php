<?php
/**
 * 工作流结点表 逻辑层
 * author:panjie 
 */
namespace WorkflowLog\Logic;
use Think\Model;
use WorkflowLog\Model\WorkflowLogModel; //工作流结点表
use Workflow\Model\WorkflowModel;		//工作流表
use Chain\Model\ChainModel;				//审核链表
use Chain\Logic\ChainLogic; 			//审核链表，用于取下一审核人信息
use Examine\Model\ExamineModel;			//审核流程表
class WorkflowLogLogic extends Model
{
	private $id;
	private $workflowLog;
	/**
	 * 存入 审核意见 并改变相关状态
	 */
	public function saveCommited($id)
	{
		if(!$this->_validate())
		{
			return false;
		} 

		$id = $this->id;
		$workflowLog = $this->workflowLog;
		
		//取工作流 信息
		$workflowId = $workflowLog['workflow_id'];
		$WorkflowM = new WorkflowModel();
		$map = array();
		$map['id'] = $workflowId;
		$workflow = $WorkflowM->where($map)->find();

		$WorkflowLogM = new WorkflowLogModel();

		//判断是否最后结点
		$chainId = $workflow['chain_id'];

		$ChainM = new ChainModel();
		$chain = $ChainM->getListById($chainId);

		//非办结结点.写入数据
		if($chain['next_id'] != '0')
		{
			//则查看是否传入user_id。该user_id是否在可选的userid列表中
			$userId = I('post.user_id');
			if(!is_numeric($userId))
			{
				$this->error = "未传入审核人员信息";
				return false;
			}

			//取用户审核列表
			$currentUserId = get_user_id();
			$ChainL = new ChainLogic();
			$userLists = $ChainL->getNextExaminUsersByUserIdAndId($currentUserId , $chainId);

			if(!isset($userLists[$userId]))
			{
				$this->error = "用户提交的审核人，未在该流程的审核列表中";
				return false;
			}

			//更新工作流chainid信息
			$data = array();
			$data["id"] = $workflowId;
			$data['chain_id'] = $chain['next_id'];
			$WorkflowM->data($data)->save();

			//添加新的工作流结点
			$data = array();
			$data['pre_id'] = $id;
			$data['workflow_id'] = $workflowId;
			$data['user_id'] = $userId;

			$WorkflowLogM = new WorkflowLogModel();
			if(!$WorkflowLogM->create($data))
			{
				$this->error = "程序内部错误，错误代码：29231。出错原因：数据创建过程出错";
				return false;
			}
			$WorkflowLogM->add();
		}
		
		//最后结点，写入意见，办结.
		//todo:取返回值 ，判断进行错误信息定制。
		else
		{
			//写工作流信息
			$data = array();
			$data['id'] = $workflowId;
			$data['state'] = '1';
			$data['is_finished'] = '1';
			$WorkflowM->data($data)->save();
		}

		//更新本工作流结点信息
		$data = array();
		$data['id'] = $id;
		$data['is_commited'] = '1';
		$data['commit'] = I('post.commit');
		$WorkflowLogM->data($data)->save();	
		return true;
	}

	/**
	 * 退回申请人
	 * */
	public function backToStart()
	{
		if(!$this->_validate())
		{
			return false;
		}

		$id = I('post.id');
		//查找第一个人是谁。
		$WorkflowLogM = new WorkflowLogModel();
		$start = $WorkflowLogM->getStartListById($id);
		$userId = $start['user_id'];

		//查找本工作流结点
		$map = array();
		$map['id'] = $id;
		$workflowLog = $WorkflowLogM->where($map)->find();

		//查找本工作流
		$workflowId = $workflowLog['workflow_id'];
		$map=array();
		$map['id'] = $workflowId;
		$WorkflowM = new WorkflowModel();
		$workflow = $WorkflowM->where($map)->find();

		//查找对应审核流程的，第一个审核链接值
		$examineId = $workflow['examine_id'];
		$ExamineM = new ExamineModel();
		$map = array();
		$map['id'] = $examineId;
		$examine = $ExamineM->where($map)->find();
		$chainId = $examine['chain_id'];

		//更新本工作流结点
		$data = array();
		$data['id'] = $id;
		$data['is_commited'] = '1';
		$data['commit'] = I('post.commit');
		$WorkflowLogM->data($data)->save();

		//添加下条工作流结点。
		$data = array();
		$data['pre_id'] = $id;
		$data['user_id'] = $userId;
		$data['workflow_id'] = $workflowId;
		if(!$WorkflowLogM->create($data))
		{
			$this->error = $WorkflowLogM->getError();
			return false;
		}
		$WorkflowLogM->add();

		//更新工作流 当前审核链结点
		$data = array();
		$data['id'] = $workflowId;
		$data['chain_id'] = $chainId;
		$WorkflowM->data($data)->save();

		return true;
	}

	/**
	 * 进行权限审核。看是否为在办、待办
	 * */
	public function _validate()
	{
		$id = I('post.id');
		if(!is_numeric($id))
		{
			$this->error = "未传入正确的id值";
			return false;
		}

		//判断当前状态，是否为未存取
		$WorkflowLogM = new WorkflowLogModel();
		$workflowLog = $WorkflowLogM->getListById($id);

		//判断是否为已办或搁置工作流
		if($workflowLog['is_commited'] == '1' || $workflowLog['is_shelved'] == '1')
		{
			$this->error = "用户在尝试操作一个状态为 已办 或 搁置 的记录";
			return false;
		}
		$this->id = $id;
		$this->workflowLog  = $workflowLog ;
		return true;
	}
}