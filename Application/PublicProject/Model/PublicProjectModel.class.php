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
	*判断一下有没有相同的名称
	*/
	public function saveProject()
	{
		$data = I('post.');
		$msg = $this->where($map)->select();
		$id = $this->add($data);
		if($id)
        	return $id;
        else
        	return false;
	}
	/*
	*从数据库去数据，追加到页面
	*/
	public function append($pid)
	{

		$map['pid'] = $pid;
		$res = $this->where($map)->select();
		return $res;
	}
	/**
	 * 判断type的方法
	 */		
	public function getTypeById($id)
	{
		$map['id'] = $id;
		$res = $this->where($map)->field('type')->find();
		return $res;
	}
}