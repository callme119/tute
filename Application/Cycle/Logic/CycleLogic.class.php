<?php
namespace Cycle\Logic;
use Cycle\Model\CycleModel;
class CycleLogic extends CycleModel
{
	protected $totalCount = 0;	//记录总数
	protected $order = array("begin_date"=>"desc"); //排序
	/**
	 * 设置排序方式
	 * @param [type] $order [description]
	 * @param [type] $by    [description]
	 */
	public function setOrder($order , $by)
	{	
		$this->order[$order] = $by;
	}
	public function getTotalCount()
	{
		return $this->totalCount;
	}

	public function getLists()
	{
		$this->totalCount = $this->count();
		return $this->order($this->order)->page($this->p,$this->pageSize)->select();
	}

	public function getAllLists()
	{
		return $this->getAllListsByState($state = 0);
	}
	/**
	 * 获取全部的记录录
	 * @param  布尔 $state 0为正常，1为不正常,
	 * @return [type]         [description]
	 */
	public function getAllListsByState($state = 0)
	{
		$state = (int)$state;
		
		if($state < 2)
		{
			$map['state'] = $state;
		}

		$this->totlaCount = $this->where($map)->count();
		return $this->where($map)->select();

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

	/**
	 * 获取所有非冻结的周期
	 * @return array 二维数且
	 */	
	public function getAllNoFreezedLists()
	{
		$map = array();
		$map['is_freezed'] = 0;
		return $this->where($map)->order($this->order)->select();
	}

	public function getCurrentList()
	{
		$map = array();
		$map['is_current'] = 1;
		return $this->where($map)->find();
	}

}