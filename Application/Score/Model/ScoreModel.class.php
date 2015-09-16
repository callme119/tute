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
	public function save($project_id)
	{
		$userid = I('post.name');
		$score_percent = I('post.score_percent');
		foreach ($userid as $key => $value) {
			$data[userid] = $value;
			$data[project_id] = $project_id;
			$data[score_percent] = $score_percent[$key];
			$userM = new UserModel();
			$data[name] = $userM->getUserById($value);
			$this->add($data);
		}
	}
}