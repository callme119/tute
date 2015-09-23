<?php
/**
 * 基础工作量管理
 */
namespace BaseTeaching\Controller;
use Admin\Controller\AdminController;
use Cycle\Logic\CycleLogic;			//周期
use BaseTeaching\Logic\UserLogic as UserBaseTeachingLogic;	//用户－－基础教学任务量 链表
use User\Logic\UserLogic;		// 用户
use Admin\Widget\UserWidget;
use BaseTeaching\Logic\BaseTeachingLogic;		//基础教学任务
use PHPExcel\Server\PHPExcelServer;
class ManageController extends AdminController
{
	public function indexAction()
	{
		try
		{
			//获取当前周期，
			$CycleL = new CycleLogic();
			if(!$cycle = $CycleL->getCurrentList())
			{
				E("未设置当前周期，请先在“周期管理”中进行当前周期的设置");
			}

			//取所有用户信息
			$UserL = new UserLogic();
			$users = $UserL->getAllLists();

			//取当前周期下数据(条件查询)
			$userId = (int)I('get.userid');
			$cycleId = $cycle['id'];
			$BaseTeachingL = new BaseTeachingLogic();
			$baseTeachings = $BaseTeachingL->getListsByCycleId($cycleId ,$userId );


			$this->assign("users",$users);
			$this->assign("cycle",$cycle);
			$this->assign("baseTeachings",$baseTeachings);
			$this->assign("js",$this->fetch("indexJs"));
			$this->assign("YZBODY",$this->fetch());
			$this->display(YZTemplate);
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
			$id = (int)I('get.id');

			//取当前记录
			$BaseTeachingL = new BaseTeachingLogic();
			$baseTeaching = $BaseTeachingL->getListById($id);
			
			//取相关用户信息
			$userId = $baseTeaching['user_id'];
			$UserL = new UserLogic();
			$user = $UserL->getListById($userId);

			//查询当前周期
			$CycleL = new CycleLogic();
			$cycle = $CycleL->getCurrentList();

			//传值 
			$this->assign("user",$user);					//用户
			$this->assign("baseTeaching",$baseTeaching);
			$this->assign("YZBODY",$this->fetch());
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
			//取当前周期值
			$CycleL = new CycleLogic();
			if(!$currentCycle = $CycleL->getCurrentList())
			{
				E("未设置当前周期，请先在“周期管理”中进行当前周期的设置");
			}

			//取有正常用户信息
			$userId = get_user_id();
			$UserL = new UserLogic();
			$users = $UserL->getAllLists();

			//取用户在当前周期下的工作量。
			$cycleId = $currentCycle['id'];
			$UserBaseTeachingL = new UserBaseTeachingLogic();
			$userBaseTeachings = $UserBaseTeachingL->getAllListsByCycleId($cycleId);

			//传值
			$this->assign("currentCycle",$currentCycle);
			$this->assign("users",$users);
			$this->assign("userBaseTeachings",$userBaseTeachings);
			$this->assign("js",$this->fetch('editAllJs'));
			$this->assign("YZBODY",$this->fetch());
			$this->display(YZTemplate);
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
			//获取当前考核周期
			$CycleL = new CycleLogic();
			if(!$cycle = $CycleL->getCurrentList())
			{
				E("尚未设置当前考核周期");
			}

			//将当前考核周期加入post
			$cycleId = $cycle['id'];
			$_POST['cycle_id'] =$cycleId;

			//将post值写入
			$BaseTeachingL = new BaseTeachingLogic();
			$BaseTeachingL->savePost();

			$this->success("操作成功",U("index?p=".I('get.p')));
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
			$BaseTeachingL = new BaseTeachingLogic();
			$BaseTeachingL->where("id = $id")->delete();
			$this->success("操作成功",U("index?p=".I('get.p')));
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
			//取出当前周期
			$CycleL = new CycleLogic();
			$cycle = $CycleL->getCurrentList();

			//依次存表
			$_POST['cycle_id'] = $cycle['id'];
			$BaseTeachingL = new BaseTeachingLogic();
			$BaseTeachingL->savePosts();

			$this->success("操作成功",U("index?p=".I('get.p')));

		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	public function exportAction(){
		$UserL = new UserLogic();
		$users = $UserL->getAllLists();
		$lists = array();
		//重新拼接users数组，去掉无用字段，且重新排序
		foreach ($users as $key => $value) {
			$lists[$key][]
		}
		$header = array('序号','教工','已完成工作量','任务工作量');
		$letter = array('A','B','C','D');
		$excel = new PHPExcelServer;
		$excel->index($users,$header,$letter);
	}
}