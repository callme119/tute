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
		var_dump($userId);
		$scorePercent = I('post.score_percent');
		if(count($userId)<2)
		{
			$this->error = "请添加完整团队人员";
			return false;
		}
		else{
			$sumPercent = 0;

			//计算总分值
			foreach ($userId as $key => $value) {
				if( !$data['project_id'] = (int)$projectId )
				{
					$this->error = "传入了空的projectid";
					return false;
				}
				if(!$data['user_id'] = (int)$value)
				{
					$this->error = "传入了空的userId";
					return false;
				}
				$sumPercent += (int)$scorePercent[$key];
				//计算分值
				$data[score_percent] = (int)ceil($scorePercent[$key]*100/$sumPercent);
				if($this->create($data)){
					$this->add();
					$res = ture;
				}
				else
					$res = false;
			}

			
			return $res;
		}
	}
}