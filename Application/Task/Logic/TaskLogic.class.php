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

 	public function savePost()
 	{
 		$_POST['id'] = (int)I('post.id');
 		$this->create(I('post.'));
 		$this->save();
 	} 

 	public function deleteById($id)
 	{
 		$map['id'] = (int)$id;
 		$this->where($map)->delete();
 	}

 	/**
 	 * 获取相关周期值的列表
 	 * 不考虑分页
 	 */

 	public function getAllListsByCycleId($cycleId)
 	{
 		$cycleId = (int)$cycleId;
 		$map['cycle_id'] = $cycleId;
 		return $this->where($map)->select();
 	}

 	/**
 	 * 保存post过来的数据
 	 * 先取controller_name值 ，以确定type类型
 	 * @return [type] [description]
 	 */
 	public function saveAllPost()
 	{
 		$userIds = I('post.user_id');
 		$map = array();
 		$map['cycle_id'] = (int)I('cycle_id');	//周期值 
 		$map['type'] = CONTROLLER_NAME;			//是哪个控制器触发的

 		$data['value'] = (int)I('post.value');	//任务值 

 		foreach($userIds as $key => $value)
 		{

 			$map['user_id'] = $value;

 			//查找，如果没有记录，先添加
 			if(!$this->where($map)->find())
 			{

 				if($this->create($map))
 				{
 					$this->add();
 				}
 				else
 				{
 					E("数据保存出错,错误信息".$this->getError());
 				}
 			}
 			$this->create($data);
 			//更新数据，将value值写入
 			$this->where($map)->save();
 		}
 	}

 	/**
 	 * 获取 用户ID 和 周期ID 相关的记录
 	 * @param  int $userId    用户ID
 	 * @param  int $cycleId 周期ID
 	 * @return array          一维
 	 */
 	public function getListByUserIdCycleId($userId,$cycleId,$type)
 	{
 		$userId = (int)$userId;
 		$cycleId = (int)$cycleId;
 		$type = trim($type);

 		$map['user_id'] = $userId;
 		$map['cycle_id'] = $cycleId;
 		$map['type'] = $type;

 		return $this->where($map)->find();
  	}
 }