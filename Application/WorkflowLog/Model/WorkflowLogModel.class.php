<?php
/**
 * 工作流日志
 * 用于记录当前流程处理人员的处理信息
 * panjie 3792535@qq.com
 */
namespace WorkflowLog\Model;
use Think\Model;
class WorkflowLogModel extends Model
{
	/**
	 * 通过userId获取待办的数据
	 * @param  [type] $userId [用户ID]
	 * @return [type]         [包括所有信息的二维数组 ]
	 */
	public function getTodoListsByUserId($userId = null)
	{
		if($userId === null)
			return false;
		$map["user_id"] = $userId;
		$map["is_commited"] = 0; //用户未提交(待办或是在办)
		$map["is_clicked"] = 0; //未点击(待办)
		$map["is_shelved"] = 0; //未搁置
		$return = $this->where($map)->select();
		return $return;
	}
	/**
	 * 依据工作流ID,获取此工作流下的所有工作流日志信息
	 * @param  [string] $workflowId [工作流ID]
	 * @return [array]             [所以关于此工作流的留痕]
	 */
	public function getListsByWorkflowId($workflowId = null)
	{
		if($workflowId === null)
		{
			$this->error = "未传入正确的ID值";
			return false;
		}
		$map['workflow_id'] = $workflowId;
		$return = $this->where($map)->select();
		return $return;
	}
}