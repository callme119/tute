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
		$userId = I('post.name');
		$scorePercent = I('post.score_percent');
		foreach ($userId as $key => $value) {
			$data[user_id] = $value;
			$data[project_id] = $projectId;
			$data[score_percent] = $scorePercent[$key];
			if($this->add($data)){
				$res = ture;
			}
			else
				$res = false;
		}
		return $res;
	}
}