<?php
/**
 * 基础工作量管理
 */
namespace BaseTeaching\Controller;
use Task\Logic\TaskLogic;
use Admin\Controller\AdminController;
use Cycle\Logic\CycleLogic;			//周期
use BaseTeaching\Logic\UserLogic as UserBaseTeachingLogic;	//用户－－基础教学任务量 链表
use User\Logic\UserLogic;		// 用户
use Admin\Widget\UserWidget;
use BaseTeaching\Logic\BaseTeachingLogic;		//基础教学任务
use PHPExcel\Server\PHPExcelServer;
class ManageController extends AdminController
{
	private $cycle;
	private $users;
	private $baseTeachings;


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

			$this->assign('userid',$userId);
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

	public function exportAllAction(){
		$user_id = I('get.userid');
		//取当前周期编号
		$CycleL = new CycleLogic();
		$cycle = $CycleL->getCurrentList();
		$cycle_id = $cycle['id'];
		//当userid为0时取所有数据
		if($user_id == 0){
			$UserL = new UserLogic();
			$users = $UserL->getAllLists();
			$lists = array();
			//取当前周期id
			
			//根绝用户id与周期id取出已完成工作量
			$baseTeachings = array();
			$BaseTeachingL = new BaseTeachingLogic();
			foreach (array_values($users) as $key => $value) {		
					$val = $BaseTeachingL->getListByUserIdCycleId($value['id'],$cycle_id);
					if($val != null){
						$baseTeachings[$value['id']] = $val;
					}else{
						$baseTeachings[$value['id']]['value'] = 0;
					}
			}
			//取用户的任务工作量
			$TaskL = new TaskLogic();
			$taskTeachings = array();
			foreach ($users as $key => $value) {
					$type = MODULE_NAME;
					$val = $TaskL->getListByUserIdCycleId($value['id'],$cycle_id,$type);
					if($val != null){
						$taskTeachings[$value['id']] = $val;
					}else{
						$taskTeachings[$value['id']]['value'] = 0;
					}
			}	
				//重新拼接users数组，去掉无用字段，且重新排序
			foreach ($users as $key => $value) {
					 $lists[$key]['key'] = $key;	
					 $lists[$key]['name'] = $value['name'];
					 $lists[$key]['baseTeachings'] = $baseTeachings[$key]['value'];
					 $lists[$key]['taskTeachings'] = $taskTeachings[$key]['value'];
			}
		}else{
			$lists = array();
			$UserL = new UserLogic();
			$BaseTeachingL = new BaseTeachingLogic();
			$TaskL = new TaskLogic();
			$lists[0]['key'] = 0;
			$lists[0]['name'] = $UserL->getListById($user_id)['name'];
			$BaseTeachingL = new BaseTeachingLogic();
			$lists[0]['baseTeachings'] = $BaseTeachingL->getListByUserIdCycleId($user_id,$cycle_id)['value'];
			$TaskL = new TaskLogic();
			$type = MODULE_NAME;
			$lists[0]['taskTeachings'] = $TaskL->getListByUserIdCycleId($user_id,$cycle_id,$type)['value'];
		}
			$excel = new PHPExcelServer;

			$width = array('12','20','20');
			$excel->setWidth($width);

			$excel->setTitle('天职师大经管学院绩效报表-教科研业绩');
			$excel->setDatas(array_values($lists));

			$header = array('教工','已完成工作量','任务工作量');
			$excel->setHeader($header);

			$key = array('name','baseTeachings','taskTeachings');
			$excel->setkey($key);
			
			
			//设置文件名
			if(I('get.userid'))
			{
				$UserL = new UserLogic();
				if(!$user = $UserL->getListById(I('get.userid')))
				{
					E("传用的用户ID$userId有误");
				}
				$userName = $user['name'];
			}
			else
			{
				$userName = "全部";
			}
			//设计统计日期以及教工名
			$subTitle = "统计日期:" . date("Y/m/d") . "  教工:" . $userName;
			$excel->setSubTitle($subTitle);

			//设定文件名
			$fileName = $typeName . "数据-". $userName . "-" . $cycle['name'] . "-" . date("YmdHi");
			$excel->setFileName($fileName);

			$excel->create();

			$row = $excel->getRow() - 1;
			$beginRow = $excel->getBeginRow();
			$endRow = $excel->getEndRow();
			$colLetters = $excel->getColLetters();

			//写入总计
			$activeSheet = $excel->getActiveSheet();
			$activeSheet->setCellValue($colLetters[0] . $row , "总计:");

			//如果有数据则写入总和,没有，则给0
			//从第6列开始,到第7列,分别求和
			for($col =1;$col<3;)
			{
				$col++;
				if($endRow > 3)
				{
					$activeSheet->setCellValue($colLetters[$col] . $row , "=sum(" . $colLetters[$col].$beginRow.":".$colLetters[$col].$endRow .")");
				}
				else
				{
					$activeSheet->setCellValue($colLetters[$col] . $row , 0);
				}
			}

			$excel->download();
		
	}
}