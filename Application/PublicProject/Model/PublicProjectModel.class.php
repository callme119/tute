<?php

/*
 * 公共项目表的Model类
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace PublicProject\Model;
use Think\Model;
class PublicProjectModel extends Model{

	/**
	*初始化方法
	*/
	public function init()
	{
		$res = $this->where('pid=0')->select();
		return $res;
	}
	/*
	*保存
	*/
	public function saveProject($data)
	{
		$this->add($data);
		$state = "success";
        return $state;
	}
	public function append($pid)
	{
		$map['pid'] = $pid;
		$res = $this->where($map)->select();
		return $res;
	}
}