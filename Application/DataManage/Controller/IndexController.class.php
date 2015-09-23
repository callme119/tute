<?php
/**
 * 数据管理
 */
namespace DataManage\Controller;
use Admin\Controller\AdminController;
use DataManage\Logic\DataManageLogic;		//数据导出逻辑
use Cycle\Logic\CycleLogic;					//周期
use Task\Logic\TaskLogic;					//任务量
use User\Logic\UserLogic;					//用户
use Project\Logic\ProjectLogic;				//项目信息
class IndexController extends AdminController
{
	protected $cycles = array(); 			//考核周期
	protected $projects = array();		//用户考核数据
	protected $users = array();				//用户
	protected $cycleId = 0;					//当前周期
	public function indexAction()
	{
		try
		{
			$this->init();
			$cycleId = $this->cycleId;
			$cycles = $this->cycles;			//取考核周期数据
			$projects = $this->projects;	//取用户项目得分信息
			$users = $this->users;				//用户信息

			//初始化用户数据
			$userDatas = array();

			//计算当前项目的完成总分数及审核中的总分数
			foreach($projects as $key => $project)
			{
				$score = (int)ceil($project['score_percent']*$project['score']/$project['sum_percent']);
				if($project['state'] == '0')
				{
					$userDatas[$project['user_id']]['doingScore'] +=  $score;
				}
				else
				{
					$userDatas[$project['user_id']]['doneScore'] +=  $score;
				}
				$userDatas[$project['user_id']]['score'] +=  $score;
			}
			
			//拼接use_name字段，同时，将userDatas表中没有的用户信息补入
			foreach($users as $key => $value)
			{
				$userDatas[$key]['user_name'] = $value['name'];
			}

			//追加总任务，并计算完成率追加
			$TaskL = new TaskLogic();
			foreach($userDatas as $key => $value)
			{
				$userId = $key;
				$value['user_id'] = $key;

				$type = CONTROLLER_NAME; //查基础科研任务
				$task = $TaskL->getListByUserIdCycleIdType($userId , $cycleId , $type);

				$value["task"] = (int)$task['value'];
				
				//总完成率
				$value['donePercent'] = (int)ceil($value["doneScore"]*100/$value["task"]);

				//总预期完成率
				$value['totalPercent'] = (int)ceil($value["score"]*100/$value["task"]);

				//传给原值 
				$userDatas[$key] = $value;
			}
			// dump($userDatas);

			$order = (string)(I("get.order"));
			switch ($order) {
				case 'doneScore': //实际得分。已审核完成的项目 
					break;
				case "score":		//预期得分，已得分加未得分
					break;
				case "totalPercent":	//预期完成率
					break;
				default:
					$type = "donePercent"; //实际完成率
					break;
			}

			//获取正反向排序规则
			$by = I("get.by") == 'asc' ? 'asc' : 'desc';
			//进行快速排序
			$userDatas = quick_sort($userDatas , $order , $by);

			//取用户信息，为0，则证明取全部信息
			$userId = (int)I('get.user_id');
			if($userId)
			{
				$temp[$userId] = $userDatas[$userId];
				$userDatas = $temp;
			}
			//计算多少条信息
			$totalCount = count($userDatas);

			//判断用户选取的当前页是超范围
			if($this->p * $this->pageSize > $totalCount)
			{
				$this->p = (int)ceil($totalCount/$this->pageSize);
			}

			//截取数据
			$offset = ($this->p-1)*$this->pageSize;
			$userDatas = array_slice($userDatas, $offset , $this->pageSize);

			$this->assign("totalCount",$totalCount);
			$this->assign("cycles",$cycles);	//考核周期数据
			$this->assign("users",$users);		//用户数据
			$this->assign("userDatas",$userDatas);//用户数据信息
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	/**
	 * 初始化
	 * 根据不同的控制器的名字，返回不同的信息
	 * @return [type] [description]
	 */
	public function init()
	{
		//取传入周期传，未传入，证明是当前周期
		$cycleId = (int)I('get.cycle_id');
		$CycleL = new CycleLogic();
		if(!$cycleId)
		{
			$cycleCurrent = $CycleL->getCurrentList();
			$cycleId = $cycleCurrent['id'];
		}

		$this->cycleId = $cycleId;	//当前周期

		//取周期列表（正常）
		$this->cycles = $CycleL->getAllListsByState();
		// dump($cycles);

		//取当前controller 送 type
		$type = CONTROLLER_NAME;

		//按type查看是否有缓存写入，如果有，直接取缓存信息，返回。
		//TODO:
		// $projects = S("projects");
		// if($projects === false)
		// {
		//取当前周期下的全部项目信息.包括：类别ID，模型ID，基础分值，
		//计算出系数，
		$DataManageL = new DataManageLogic();
		$projects = $DataManageL->getAllListsByCycleIdType($cycleId , $type);
		if($projects === false)
		{
			E("列表读取发生错误，当前cyclid:$cycleId,当前type：$type.错误信息：" . $DataManageL->getError());
		}
		$this->projects = $projects;

		//取出所有的正常用户信息,将userDatas中没有信息的，进行置0处理。
		$UserL = new UserLogic();
		$users = $UserL->getAllLists();
		$this->users = $users;
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
			$this->success("操作成功",U("index?p=".I('get.p')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	public function detailAction()
	{
		try
		{
			//取用户信息
			$userId = (int)I("get.user_id");

			$UserL = new UserLogic();
			if(!$user = $UserL->getListById($userId))
			{
				E("未接到到正确的用户ID，当前USERID:$userId");
			}

			//获取当前周期信息
			$CycleL = new CycleLogic();
			if(!$currentCycle = $CycleL->getCurrentList())
			{
				E("未设置当前周期，或设置的当前周期不可用");
			}
	
			//取当前周期下用户的项目信息
			$cycleId = $currentCycle['id'];
			$ProjectL = new ProjectLogic();
			$projects = $ProjectL->getListsByUserIdCycleId($userId , $cycleId);
			$totalCount = $ProjectL->getTotalCount();

			dump($projects);
			$this->assign("projects",$projects);
			$this->assign("totalCount",$totalCount);
			$this->assign("YZBODY",$this->fetch('Index/detail'));
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
}