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
		$order = 'begin_time desc';
		$this->totalCount = $this->count();
		return $this->order($order)->page($this->p,$this->pageSize)->select();
	}

	public function savePost()
	{
		return $this->save($data);
	}

	public function addPost()
	{
		if($this->create())
		{
			$this->add();
		}
		else
		{
			E($this->getError());
		}
	}

	public function deleteById($id)
	{
		$map['id'] = $id;
		return $this->map($id)->delete();
	}
}