<?php
/**
 * 基础教学工作量设置
 */
namespace Task\Controller;
use Admin\Controller\AdminController;	//
use Cycle\Logic\CycleLogic;			//周期
use Task\Logic\UserLogic;			//任务-用户
use Task\Logic\TaskLogic;			//任务
class IndexController extends AdminController
{
	public function indexAction()
	{
		try
		{
			//找出所有的周期，按开始时间倒着排序。
			$CycleL = new CycleLogic();
			$cycles = $CycleL->getAllNoFreezedLists();
			// dump($cycles);

			$cycleId = (int)I('get.cycleid');
			if(!$cycleId)
			{
				//找出当前周期，如果没有，就返回所有的最前面那个
				if(!$currentCycle = $CycleL->getCurrentList())
				{
					$currentCycle = $cycles[0];
				}
				$cycleId = $currentCycle['id'];
			}
			
			$UserL = new UserLogic();
			$userTasks = $UserL->getJoinUserListsByCycleId($cycleId);
			$totalCount = $UserL->getTotalCount();
			// dump($userTasks);

			//还需要传一个type值到V层。
			$this->assign("type","baseTeaching");
			$this->assign("cycleId",$cycleId);
			$this->assign("cycles",$cycles);
			$this->assign("userTasks",$userTasks);
			$this->assign("totalCount",$totalCount);
			$this->assign("js",$this->fetch('Index/indexJs'));
			$this->assign("YZBODY",$this->fetch('Index/index'));
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
			$id = (int)I('get.id');

			//取记录
			$TaskL = new TaskLogic();
			if(!$task = $TaskL->getListById($id))
			{
				E("传入的ID值有误，或您要删除的记录不存在");
			}

			$this->assign("task",$task);
			$this->assign("YZBODY",$this->fetch('Index/edit'));
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
	
	public function editAllAction()
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

	public function saveAllAction()
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

	public function saveAction()
	{
		try
		{
			dump(I('post.'));
			//根据ID保存VALUE值。

			// $this->success("操作成功",U("index?cycleid='.I('get.cycleid').'&p=".I('get.p')));
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