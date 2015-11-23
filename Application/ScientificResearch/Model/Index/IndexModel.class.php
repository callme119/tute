<?php

namespace ScientificResearch\Model\Index;

use ScientificResearch\Model;
use Workflow\Logic\WorkflowLogic;           //工作流
use WorkflowLog\Logic\WorkflowLogLogic;    //工作流 详情
use Chain\Logic\ChainLogic;        //审核流

class IndexModel
{
    protected $errors = array();
    protected $tips = array();
    protected $waring = array();
    protected $project = null;

    public function getErrors()
    {
        return $this->errors;
    }

    public function getTips()
    {
        return $this->tips;
    }

    public function setProject($project)
    {
        $this->project = $project;
    }
    /**
     * 查看项目的状态是否可被编辑
     * @param  [array]  $project [项目信息]
     * @return boolean          [0 不可被编辑  1 可被编辑]
     */
    public function isEdit($project)
    {
        if (!isset($project))
        {
            $this->errors[] = "indexModel参数未传入";
            return false;
        }

        //查看当前项目申请人，是否为当前用户
        $userId = get_user_id();
        if($userId !== $project['commit_user_id'])
        {
            $this->tips[] = '当前用户$userId,当前项目申请人ID' . $project[commit_user_id];
            return false;
        }

        //查看该项目是否为未提交项目
        $WorkflowL = new WorkflowLogic();
        if (!$WorkflowL->getListByProjectId($project[id]))
        {
            return true;
        }
        

        if (!$this->getWorkflowStatus($project[id]))
        {
            $this->tips[] = "当前项目非首结点，不能编辑";
            return false;
        }
        
        return true;
    }

    /**
     * 获取项目的工作流状态
     * @param  id $projectId 项目id
     * @return 如果为工作首结点，true
     * 其它 false            
     */
    protected function getWorkflowStatus($projectId)
    {
        //取项目当前工作流
        $WorkFlowL = new WorkFlowLogic();
        $workFlow = $WorkFlowL->getListByProjectId($projectId);

        //取当前对应审核流
        $chainId = $workFlow['chain_id'];
        $ChainL = new ChainLogic();
        $chain = $ChainL->getListByID($chainId);
        if($chain[pre_id] !== '0')
        {  
            $this->tips[] = "当前审核结点非首结点";
            return false;
        }
        return true;
    }
}
