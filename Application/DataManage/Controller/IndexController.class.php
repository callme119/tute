<?php
/**
 * 数据管理
 */
namespace DataManage\Controller;
use Admin\Controller\AdminController;
use DataManage\Logic\DataManageLogic;		//数据导出逻辑
use Cycle\Logic\CycleLogic;					//周期
use Task\Logic\TaskLogic;		//任务量
class IndexController extends AdminController
{
	public function indexAction()
	{
		try
		{
			//取传入周期传，未传入，证明是当前周期
			$cycleId = (int)I('get.cycle_id');
			if(!$cycleId)
			{
				$CycleL = new CycleLogic();
				$cycleCurrent = $CycleL->getCurrentList();
				$cycleId = $cycleCurrent['id'];
			}

			//取周期列表（正常）
			$cycles = $CycleL->getAllListsByState();
			// dump($cycles);

			//取当前controller 送 type
			$type = CONTROLLER_NAME;

			//按type查看是否有缓存写入，如果有，直接取缓存信息，返回。
			//TODO:

			//取当前周期下的全部项目信息.包括：类别ID，模型ID，基础分值，
			//计算出系数，
			$DataManageL = new DataManageLogic();
			$dataManages = $DataManageL->getAllListsByCycleIdType($cycleId , $type);
			if($dataManages === false)
			{
				E("列表读取发生错误，当前cyclid:$cycleId,当前type：$type.错误信息：" . $DataManageL->getError());
			}

			// dump($dataManages);
			//对用户进行分类运算,计算出总分
			$users = array();
			foreach($dataManages as $key => $dataManage)
			{
				$score = (int)ceil($dataManage['score_percent']*$dataManage['score']/$dataManage['sum_percent']);
				if($dataManage['state'] == '0')
				{
					$users[$dataManage['user_id']]['doingTotalScore'] +=  $score;
				}
				else
				{
					$users[$dataManage['user_id']]['doneTotalScore'] +=  $score;
				}
				$users[$dataManage['user_id']]['totalScore'] +=  $score;
			}

			//追加总任务，并计算完成率追加
			$TaskL = new TaskLogic();
			foreach($users as $key => $value)
			{
				$userId = $key;
				$value['user_id'] = $key;

				$type = "BaseScientificResearch"; //查基础科研任务
				$task = $TaskL->getListByUserIdCycleIdType($userId , $cycleId , $type);
				$value[$type . "Task"] = (int)$task['value'];

				//计算基础科研完成率
				$value['baseScientificResearchPercent'] = (int)ceil($value['doneTotalScore']*100/$value['BaseScientificResearchTask']);
				$type = "PostScientificResearch"; //查岗位科研任务
				$task = $TaskL->getListByUserIdCycleIdType($userId , $cycleId , $type);
				$value[$type . "Task"] = (int)$task['value'];

				//计算总任务
				$value["totalTask"] = $value["BaseScientificResearchTask"] + $value["PostScientificResearchTask"];

				//计算岗位科研完成率
				$remain = (int)$value['doneTotalScore']-$value['BaseScientificResearchTask'];
				$remain = $remain > 0 ? $remain : 0;
				$value['postScientificResearchPercent'] = (int)ceil($remain*100/$value['postScientificResearch']);

				//总完成率
				$value['donePostScientificResearchPercent'] = (int)ceil($value["doneTotalScore"]*100/$value["totalTask"]);

				//总预期完成率
				$value['totalPostScientificResearchPercent'] = (int)ceil($value["totalScore"]*100/$value["totalTask"]);

				//传给原值 
				$users[$key] = $value;
			}
			dump($users);
			//取用户信息，为0，则证明取全部信息
			$userId = (int)I('get.user_id');
			if(!$userId)
			{
				//取个人信息
			}
			
			//取单个用户信息在当前周期 当前type下的所有信息的综合分值
			//将数值写入缓存（每点一次就查一次，这受不了呀）
			
			$this->assign("cycles",$cycles);
			$this->assign("YZBODY",$this->fetch('Index/index'));
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
}