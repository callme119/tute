<?php
/**
 * 基础教学工作量设置
 */
namespace Task\Controller;
use Cycle\Logic\CycleLogic;			//周期
class BaseTeachingController extends IndexController
{
	public function indexAction()
	{
		try
		{
			parent::indexAction();
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	public function addAction()
	{
		try
		{
			$this->assign("YZBODY",$this->fetch());
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
	
	public function editAction()
	{
		try
		{
			parent::editAction();
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	public function deleteAction()
	{
		try
		{
			$this->success("操作成功",U("index?p=".I('get.p')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
}