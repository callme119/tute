<?php
/**
 * 教科研数据管理
 * panjie
 * 2015 9 18
 */
namespace DataManage\Controller;
use DataManage\Logic\DataManageLogic;		//数据导出逻辑
use Cycle\Logic\CycleLogic;					//周期
use Task\Logic\TaskLogic;					//任务量
use User\Logic\UserLogic;					//用户
class ScientificResearchController extends IndexController
{
	public function indexAction()
	{
		try
		{
			parent::init();
			$cycleId = $this->cycleId;
			$cycles = $this->cycles;			//取考核周期数据
			$projetcs = $this->projects;	//取用户项目得分信息
			$users = $this->users;				//用户信息

			//进行数据缓存
			// S("projetcs" , $projetcs , 60*60);
			// S("projetcsTime" , time());
			// }
			// echo S("projetcsTime");
			// $lastCacheTime = S("projetcsTime");

			// dump($projetcs);
			//对用户进行分类运算,计算出总分
			
			$userDatas = array();
			$totalScore	= 0 ;  //预期完成总分（已审核 + 未审核）
			$totalDoneScore = 0; //已完成总分
			foreach($projetcs as $key => $project)
			{
				$score = (int)ceil($project['score_percent']*$project['score']/$project['sum_percent']);
				if($project['state'] == '0')
				{
					$userDatas[$project['user_id']]['doingScore'] +=  $score;
				}
				else
				{
					$userDatas[$project['user_id']]['doneScore'] +=  $score;
					$totalDoneScore += $score;
				}
				$userDatas[$project['user_id']]['score'] +=  $score;
				$totalScore += $score;
			}
			
			//拼接use_name字段，同时，将userDatas表中没有的用户信息补入
			foreach($users as $key => $value)
			{
				$userDatas[$key]['user_name'] = $value['name'];
			}

			//追加总任务，并计算完成率追加
			$TaskL = new TaskLogic();
			$totalTask = 0;	//总任务数
			foreach($userDatas as $key => $value)
			{
				$userId = $key;
				$value['user_id'] = $key;

				$type = "BaseScientificResearch"; //查基础科研任务
				$task = $TaskL->getListByUserIdCycleIdType($userId , $cycleId , $type);
				// dump($task);
				$value["BaseTask"] = (int)$task['value'];

				//计算基础科研完成率
				$value['basePercent'] = (int)ceil($value['doneScore']*100/$value['BaseTask']);
				$type = "PostScientificResearch"; //查岗位科研任务
				$task = $TaskL->getListByUserIdCycleIdType($userId , $cycleId , $type);
				$value["PostTask"] = (int)$task['value'];

				//计算总任务
				$value["totalTask"] = $value["BaseTask"] + $value["PostTask"];
				$totalTask += $value["totalTask"];
				//计算岗位科研完成率
				$remain = (int)$value['doneScore']-$value['BaseTask'];
				$remain = $remain > 0 ? $remain : 0;

				//岗位业绩完成率
				$value['postPercent'] = (int)ceil($remain*100/$value['postScientificResearch']);

				//总完成率
				$value['donePercent'] = (int)ceil($value["doneScore"]*100/$value["totalTask"]);

				//总预期完成率
				$value['totalPercent'] = (int)ceil($value["score"]*100/$value["totalTask"]);

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

			//取单个用户信息在当前周期 当前type下的所有信息的综合分值
			//将数值写入缓存（每点一次就查一次，这受不了呀）
			
			$this->assign("totalTask",$totalTask);				//总任务
			$this->assign("totalScore",$totalScore);			//预期完成总分
			$this->assign("totalDoneScore",$totalDoneScore);	//已完成总分
			$this->assign("totalCount",$totalCount);			//共多少条
			$this->assign("cycles",$cycles);					//周期列表
			$this->assign("users",$users);						//所有教工信息
			$this->assign("userDatas",$userDatas);				//数据信息
			$this->assign("lastCacheTime",$lastCacheTime);		//上次数据缓存时间
			$this->assign("js",$this->fetch("Index/indexJs"));
			$this->assign("YZBODY",$this->fetch('index'));
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
}