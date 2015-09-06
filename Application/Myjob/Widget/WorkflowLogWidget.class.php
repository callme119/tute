<?php
/**
 * 工作流结点明细表
 */
namespace Myjob\Widget;
use WorkflowLog\Model\WorkflowLogModel;
use User\Model\UserModel;
class WorkflowLogWidget {
    /**
     * 通过ID值，获取用户信息
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getListByIdAction($id = null){
        $WorkflowLogM = new WorkflowLogModel();
        return $WorkflowLogM->getListById($id);
    }

    /**
     * 通过wrokflowLogId值，获取该值下的user_id对应的用户名
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function getUserNameByIdAction($id = null)
    {
        if($id == null || !is_numeric($id) || $id == '0')
        {
            echo '-';
            return;
        }
        $WorkflowLogM = new WorkflowLogModel();
        $workflowLog = $WorkflowLogM->getListById($id);

        $userId = $workflowLog['user_id'];
        $UserM = new UserModel();
        $user = $UserM->getListById($userId);
        echo $user['name'];

    }

}