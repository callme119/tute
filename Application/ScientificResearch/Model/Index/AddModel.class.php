<?php

namespace ScientificResearch\Model\Index;

use ScientificResearch\Model;
use Workflow\Logic\WorkflowLogic;           //工作流
use WorkflowLog\Logic\WorkflowLogLogic;     //工作流 详情
use Chain\Logic\ChainLogic;                 //审核流
use Score\Logic\ScoreLogic;                 //团队项目分配表
use User\Logic\UserLogic;                   //用户表
use Think\Controller;

class AddModel extends IndexModel
{
    protected $userId;
    protected $project;             //项目信息
    protected $workflow;            //工作流
    protected $examineId;           //审核流程ID
    protected $checkUserId;         //审核人ID
    protected $scores;              //团队成员得分
    protected $isUnsubmit;          //是否为未提交的项目
    protected $suggestions;         //审核意见
    protected $nextExamineUsers;   //结点下一审核人

    public function getUserId()
    {
        if (isset($this->userId))
        {
            return $this->userId;
        }
        else
        {  
            $this->errors[] = "请先使用setUserId传入用户ID";
            return false;
        }
    }

    /**
     * 取一下审核人员信息
     * @return array 包含当前流程的审核信息
     */
    public function getNextExamineUsers()
    {
        if (isset($this->nextExamineUsers))
        {
            return $this->nextExamineUsers;
        }

        //取工作流信息 当前用户信息
        $workflow = $this->getWorkflow();
        $userId = $this->userId;

        //取审核链信息，得到审核人员
        $ChainL = new ChainLogic();
        if (!$this->nextExamineUsers = $ChainL->getNextExaminUsersByUserIdAndId($userId , $workflow['chain_id']))
        {
            $this->errors[] = "取出审核人员信息发生错误，错误信息" . $ChainL->getError();
            return false;
        } 
        
        return $this->nextExamineUsers;

    }
    public function getUserNameByUserId($userId)
    {  
        $UserL = new UserLogic();
        $user = $UserL->getListById($userId);
        return $user[name];
    }

    public function getSuggestions()
    {
        if (isset($this->suggestions))
        {
            return $this->suggestions;
        }

        if (!$projectId = $this->project[id])
        {
            $this->errors[] = "请先使用setProject方法，传入project";
            return false;
        }

        //取workflow信息
        $workflow = $this->getWorkflow();
        $workflowId = $workflow[id];

        //取工作流日志信息
        $WorkflowLogL = new WorkflowLogLogic();
        $this->suggestions = $WorkflowLogL->getListsByWorkflowId($workflowId);
        
        return $this->suggestions;
    }
    /**
     * 是否为未提交的项目
     * @return 未提交过 1，已提交过0
     */
    public function getIsUnsubmit()
    {
        if (isset($this->isUnsubmit))
        {
            return $this->isUnsubmit;
        }

        if (!$projectId = $this->project[id])
        {
            $this->errors[] = "请先使用setProject方法，传入project";
            return false;
        }

        $WorkflowL = new WorkflowLogic();
        if ($workflow = $WorkflowL->getListByProjectId($projectId))
        {
            $this->isUnsubmit = false;
        }
        else
        {
            $this->isUnsubmit = true;
        }
        return $this->isUnsubmit;
    }
    /**
     * 获取当前id的工作流信息
     * @return list 工作流
     */
    public function getWorkflow()
    {
        if( isset($this->workflow) )
        {
            return $this->workflow;
        }

        if (!isset($this->project))
        {
            $this->errors[] = "请先使用setProject方法，传入project";
            return false;
        }

        $projectId = $this->project[id];
        $WorkflowL = new WorkflowLogic();
        if (!$this->workflow  = $WorkflowL->getListByProjectId($projectId))
        {
            $this->errors[] = $WorkflowL->getError();
            return false;
        }

        return $this->workflow;
    }

    public function getExamineId()
    {
        if (isset($this->examineId))
        {
            return $this->examineId;
        }

        if (!isset($this->workflow))
        {
            if( !$this->getWorkflow() )
            {
                $this->errors[] = "未能获取workflow信息";
                return false;
            }
        }

        return $this->workflow[examine_id];
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getProjectTitle()
    {
        return $this->project['title'];
    }

    public function getProjectId()
    {
        return $this->project['id'];
    }
    /**
     * 查看当前的 项目 当前 用户 是否可编辑 
     * @param  object  $project 项目
     * @param  int $userId  用户ID
     * @return 当前用户可以编辑当前项目 1
     * 其它 0
     */
    public function isEdit($project = null, $userId = null)
    {
        if($project === null)
        {
            $project = $this->project;
        }

        if($userId === null)
        {
            $userId = $this->userId;
        }

        //查看该项目是否为未提交项目
        $WorkflowL = new WorkflowLogic();
        if (!$WorkflowL->getListByProjectId($project[id]))
        {
            return true;
        }

        if($project[user_id] !== $userId )
        {
            $this->tips[] = "当前项目的申请人:" . $project[user_id] . "非当前用户$userId";
            return false;
        }

        if (!$this->getWorkflowStatus($project[id]))
        {
            $this->tips[] = "当前项目非首结点，不能编辑";
            return false;
        }

        return true;
    }

    /**
     * 判断是否当前项目类别
     * @param  it  $projectCategoryId 项目类别ID
     * @return 是 true 
     */
    public function isCurrentProjectCategory($projectCategoryId)
    {
        if ($this->project['project_category_id'] == $projectCategoryId)
        {
            return true;
        }

        return false;
    }

    /**
     * [getTeamers description]
     * @return [type] [description]
     */
    public function getScores()
    {

        if ( isset($this->scores) )
        {
            return $this->scores;
        }

        $projectId = $this->project[id];
        if ( !isset($projectId))
        {
            $this->errors[] = "请先使用setProject方法，传入project" ;
            return false;
        }

        $ScoreL = new ScoreLogic();
        if (!$this->scores = $ScoreL->getAllListsByProjectId($projectId))
        {
            $this->errors[] = "未获取到团队成员信息";
            return false;
        }

        return $this->scores;
    }
}