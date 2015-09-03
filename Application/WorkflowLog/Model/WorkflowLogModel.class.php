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
		$map["is_commited"] = 0; //用户未提交
		$map["is_finished"] = 0; //未完成
		$map["is_shelved"] = 0; //未搁置
		$return = $this->where($map)->select();
		return $return;
	}
}