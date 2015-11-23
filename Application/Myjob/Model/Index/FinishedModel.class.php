<?php
/**
 * 已办工作
 */
namespace Myjob\Model\Index;

use WorkflowLog\Logic\WorkflowLogLogic;            //审核详情表

class FinishedModel
{
    private $userId;
    private $error;
    private $errors = array();

    public function getErrors()
    {
        return $this->errors;
    }

    public function getUserId()
    {
        if (!isset($this->userId))
        {
            $this->errors[] = "userId值未取出";
            return false;
        }

        return $this->userId;
    }

    /**
     * 判断当前审核流程详情是否可以取回
     * @param  INT $workflowLogId 工作流详情表
     * @param  int $userId        用户ID
     * @return bool               可取回true 不可取回false
     */
    public function getIsBack($workflowLogId, $userId = null)
    {
        if (!isset($userId))
        {
            $this->userId = get_user_id();
        }
        else
        {
            $this->userId = (int)$userId;
        }

        //取出当前审核详情的 审核信息
        $WorkflowLogL = new WorkflowLogLogic();

        //取当前审核流程的当前结点信息
        $workflowLog = $WorkflowLogL->getListById($workflowLogId);
        if ($workflowLog[user_id] != $this->userId)
        {
            $this->errors[] = "当前流程".$workflowLog[id] . "不属于当前用户" . $this->userId;
            return false;
        }

        //取当前审核流程的上一审核人员信息
        $currentWorkflowLog = $WorkflowLogL->getCurrentListByWorkflowId($workflowLog['workflow_id']);

        //上一审核结点信息为当前用户
        if ($currentWorkflowLog[pre_id] !== $workflowLog[id])
        {
            $this->errors[] = "当前流程的上一审核人员非当前用户，不可取回";
            return false;
        }

        //看下一审核人是否已经点击 
        if($currentWorkflowLog['is_clicked'] == '1')
        {
            $this->errors[] = "当前流程下一审核人已查看，无法取回";
            return false;
        }

        return true;
    }
}