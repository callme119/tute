<?php

/*
 * User用户Model
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace User\Model;
use Think\Model;
class UserModel extends Model{
	public function getAllName()
	{
		$res = $this->field('name,userid')->select();
		return $res;
	}
}