<?php

/*
 * 数据模型一的Model类
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace DatamodelOne\Model;
use Think\Model;
class DatamodelOneModel extends Model{
	public function save($id)
	{
		$data = I('post.');
		$data['cid'] = $id;
		if($this->add($data)){
			return true;
		}
		else
			return false;
	}

}