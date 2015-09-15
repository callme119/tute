<?php
namespace Cycle\Logic;
use Cycle\Model\CycleModel;
class CycleLogic extends CycleModel
{
	protected $totalCount = 0;	//记录总数

	public function getTotalCount()
	{
		return $this->totalCount;
	}

	public function getLists()
	{
		$order = 'begin_date desc';
		$this->totalCount = $this->count();
		return $this->order($order)->page($this->p,$this->pageSize)->select();
	}

	public function savePost()
	{
		$data = I('post.');
		$data = lists_date_format_to_int($data);

		return $this->save($data);
	}

	public function addPost()
	{
		$data = I('post.');

		//将时间进行INT转换
		$data = lists_date_format_to_int($data);
		

		if($this->create($data))
		{
			$this->add();
		}
		else
		{	dump($data);
			E($this->getError());
		}
	}

	public function deleteById($id)
	{
		$map['id'] = (int)$id;
		return $this->where($map)->delete();
	}

	public function getListById($id)
	{
		$id = (int)$id;
		return $this->where("id = $id")->find();
	}

	/**
	 * 将原来的当前周期置为1 。
	 * @return [type] [description]
	 */
	public function deleteCurrentLists()
	{
		$map = array();
		$map['is_current'] = 1;
		$data = array();
		$data['is_current'] = 0;
		$this->where($map)->save($data);
		return;
	}

	/**
	 * 将当前ID设置为当前周期 
	 */
	public function setCurrentListById($id)
	{
		$data= array();
		$data['id'] = (int)$id;
		$data['is_current'] = 1;
		$this->save($data);
		return;
	}

	/**
	 * 取出所有的一级周期列表
	 */
	public function getRootlists($id = 0 , $is_freezed = 0)
	{
		$map = array();
		$map['pid'] = (int)$id;
		$map['is_freezed'] = '0';
		return $this->where($map)->select();
	}

	/**
	 * 获取某结点的下级结点
	 */
	public function getListByPid($pid)
	{
		$map = array();
		if(!$pid = (int)$pid)
		{
			E("传入的pid为0，没有理由这样做。");
		}

		$map['pid'] = $pid;
		return $this->where($map)->select();

	}

}