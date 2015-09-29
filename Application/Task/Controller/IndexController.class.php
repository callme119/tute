<?php
/**
 * 基础教学工作量设置
 */
namespace Task\Controller;
use Admin\Controller\AdminController;	//
use Cycle\Logic\CycleLogic;			//周期
use Task\Logic\UserLogic;			//任务-用户
use Task\Logic\TaskLogic;			//任务
use User\Logic\UserLogic as UserL;			//用户
class IndexController extends AdminController
{
	protected $type;			//记录正在操作的类型
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
			
			$cycleId = (int)I('get.cycleid');

			//取当前周期的状态
			$CycleL = new CycleLogic();
			$cycle = $CycleL->getListById($cycleId);

			//判断所选周期状态
			if(!$cycle || $cycle["is_freezed"] !== '0')
			{
				E("传入不正确的考核周期值cycleid,该存不存在，或是已冻结。");
			}

			//取出当前周期、当前 类别下的 任务值
			$TaskL = new TaskLogic();
			$type = CONTROLLER_NAME;
			$tasks = $TaskL->getAllListsByCycleIdType($cycleId , $type);

			$state = "1";
			$UserL = new UserL();
			$users = $UserL->getAllListsByState($state);

			//替除已有任务的教工。
			foreach($tasks as $value)
			{
				unset($users[$value['user_id']]);
			}

			$this->assign("unSetedUsers",$users);//未设置任务的用户
			$this->assign("tasks",$tasks);		//已设置任务的用户
			$this->assign("cycle",$cycle);			//用户选择周期
			$this->assign('css',$this->fetch("Index/editAllCss"));
			$this->assign("js",$this->fetch('Index/editAllJs'));
			
			$this->assign("YZBODY",$this->fetch('Index/editAll'));
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
			$TaskL = new TaskLogic();
			$TaskL->saveAllPost();
			$this->success("操作成功",U("index?p=".I('get.p') . "&cycleid=" . I('get.cycleid')));
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
			//根据ID保存VALUE值。
			$TaskL = new TaskLogic();
			$TaskL->savePost();
			$this->success("操作成功",U("index?cycleid=" .I('get.cycleid') . "&p=".I('get.p')));
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
			$id = (int)I('get.id');
			$TaskL = new TaskLogic();
			$TaskL->deleteById($id);
			$this->success("操作成功",U("index?cycleid=" .I('get.cycleid') . "&p=".I('get.p')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
}