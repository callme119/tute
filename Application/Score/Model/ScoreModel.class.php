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
	public function save($userId,$projectId)
	{
		$userId = I('post.name');
		$scorePercent = I('post.score_percent');
		foreach ($userId as $key => $value) {
			$data[project_id] = $projectId;
			//判断是不是团队项目
			//如果不是团队项目，百分比为100%
			if(count($scorePercent[$key])==1 && $data[user_id]=="请选择" && $scorePercent[$key]=="")
			{
				$data[user_id] = $userId;
				$data[score_percent] = '100';
			}
			else{
				$data[user_id] = $value;
				$data[score_percent] = $scorePercent[$key];
			}
			if($this->add($data)){
				$res = ture;
			}
			else
				$res = false;
		}
		return $res;
	}
}