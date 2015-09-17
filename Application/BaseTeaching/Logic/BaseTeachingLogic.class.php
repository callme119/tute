<?php
/**
 * 基础教学工作量 LOGIC
 */
namespace BaseTeaching\Logic;
use BaseTeaching\Model\BaseTeachingModel;
class BaseTeachingLogic extends BaseTeachingModel
{
	/**
	 * 保存包括有user_id数组的post信息
	 * @return [type] [description]
	 */
	public function savePosts()
	{
		$map['cycle_id'] = (int)I("post.cycle_id");

		$data['cycle_id'] = (int)I("post.cycle_id");
		$data['value']	=	(int)I("post.value");

		foreach(I("post.user_id") as $userId)
		{

			$data['user_id'] = $userId;
			$map['user_id'] = $userId;


			//先查有没有值
			if(!$this->where($map)->find())
			{
				if(!$this->create($data))
				{
					E($this->getError());
				}
				$this->add();
			}
			else
			{
				$this->create($data);
				$this->where($map)->save();
			}
		}
		return;
	}

	/**
	 * 取某个周下的当前页数据
	 * @param  int $cycleId  考核周期ID
	 * @return arrary           二维数组
	 */
	public function getListsByCycleId($cycleId , $userId = 0)
	{
		$cycleId = (int)$cycleId;
		if($userId = (int)$userId)
		{
			$map['user_id'] = $userId;
		}
		$map['cycle_id'] = $cycleId;
		return $this->where($map)->page($this->p,$this->pageSize)->select();
	}

	/**
	 * 获取 用户ID 和  考核周期ID 的相关记录
	 * @param  int $userId  用户ID
	 * @param  int $cycleId 周期ID
	 * @return array          一维
	 */
	public function getListByUserIdCycleId($userId , $cycleId)
	{
		$map['user_id'] = (int)$userId;
		$map['cycle_id'] =(int)$cycleId;

		return $this->where($map)->find();
 	}

 	/**
 	 * 保存post信息
 	 * @return [type] [description]
 	 */
 	public function savePost()
 	{
 		if(!$this->create())
 		{
 			E("保存数据出错，错误信息:" . $this->getError());
 		}
 		$this->save();
 	}

 	public function getListById($id)
 	{
		$map['id'] = (int)$id;
		return $this->where($map)->find();
 	}
}