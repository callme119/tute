<?php

/*
 * 数据模型一的Model类
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace DataModelOne\Model;
use Think\Model;
class DataModelOneModel extends Model{
	public function save($id)
	{
		$data['score'] = I('post.score');
		$data['project_category_id'] = $id;
		if($this->add($data)){
			return true;
		}
		else
			return false;
	}

}