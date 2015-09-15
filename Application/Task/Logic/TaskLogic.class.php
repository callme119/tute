<?php
/**
 * 任务设置 LOGIC
 */
 namespace Task\Logic;
 use Task\Model\TaskModel;			//任务
 use User\Model\UserModel;			//用户
 class TaskLogic extends TaskModel
 {
 	public function getListById($id)
 	{
 		$id = (int)$id;
 		$map['id'] = $id;
 		return $this->where($map)->find();
 	}
 }