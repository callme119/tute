<?php
/*
 * 分值占分比的Model类
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace Score\Model;
use Think\Model;
use User\Model\UserModel;
class ScoreModel extends Model{
	public function save($projectId)
	{	
		//判断是不是传入了多条数据
		//如果不是报错
		//如果是保存
		$userId = I('post.name');
		$scorePercent = I('post.score_percent');
		$sumPercent = 0;
			//计算总分值
		foreach ($userId as $key => $value) {
			if( !$data['project_id'] = (int)$projectId )
			{
				$this->error = "传入了空的projectid";
				return false;
			}
			if(!$data[user_id] = (int)$value)
			{
				$this->error = "传入了空的userId";
				return false;
			}
			$sumPercent += (int)$scorePercent[$key];
		}
			//添加数据
		foreach ($userId as $key => $value)
		{
				//计算分值
			$data[score_percent] = (int)floor($scorePercent[$key]*100/$sumPercent + 0.5);
			$data['user_id'] = $value;
			if($this->create($data)){
				$this->add();
				$res = ture;
			}
			else
				$res = false;
		}
		return $res;
		
	}
	public function addOneself($projectId,$userId)
	{
		$data[user_id] = (int)$userId;
		$data[score_percent] = (int)0;
		$data[project_id] = (int)$projectId;
		return $this->add($data);

	}
}