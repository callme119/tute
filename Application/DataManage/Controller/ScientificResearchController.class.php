<?php
/**
 * 教科研数据管理
 * panjie
 * 2015 9 18
 */
namespace DataManage\Controller;
vendor('PHPExcel.PHPExcel');				//引入phpexecl
use DataManage\Logic\DataManageLogic;		//数据导出逻辑
use Cycle\Logic\CycleLogic;					//周期
use Task\Logic\TaskLogic;					//任务量
use User\Logic\UserLogic;					//用户
use PHPExcel\Server\PHPExcelServer;			//PHPEXCEL
use PHPExcel\Server\IOFactoryServer;//PHPEXCEL
class ScientificResearchController extends IndexController
{
	protected $cycleId;
	protected $totalTask;
	protected $totalScore;
	protected $totalDoneScore;
	protected $totalCount;
	protected $cycles;
	protected $users;
	protected $userDatas;
	protected $lastCacheTime;


	public function indexAction()
	{
		try
		{
			//计算数值.只所以将其分离,原因是在数据导出中,也需要该信息
			$this->index($action = "index");
			//TODO:将数值写入缓存（每点一次就查一次，这受不了呀）
			
			$this->assign("cycleId",$this->cycleId);
			$this->assign("totalTask",$this->totalTask);				//总任务
			$this->assign("totalScore",$this->totalScore);			//预期完成总分
			$this->assign("totalDoneScore",$this->totalDoneScore);	//已完成总分
			$this->assign("totalCount",$this->totalCount);			//共多少条
			$this->assign("cycles",$this->cycles);					//周期列表
			$this->assign("users",$this->users);						//所有教工信息
			$this->assign("userDatas",$this->userDatas);				//数据信息
			$this->assign("lastCacheTime",$this->lastCacheTime);		//上次数据缓存时间
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

	public function index($action = 'index')
	{
		parent::init();
		$cycleId = $this->cycleId;
		$cycles = $this->cycles;			//取考核周期数据
		$project = $this->projects;	//取用户项目得分信息
		$users = $this->users;				//用户信息

		//进行数据缓存
		// S("project" , $project , 60*60);
		// S("projectTime" , time());
		// }
		// echo S("projectTime");
		// $lastCacheTime = S("projectTime");

		// dump($project);
		//对用户进行分类运算,计算出总分
		
		$userDatas = array();
		$totalScore	= 0 ;  //预期完成总分（已审核 + 未审核）
		$totalDoneScore = 0; //已完成总分
		foreach($project as $key => $project)
		{
			$score = $project['score'];
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

		//如果是ACTION则截取数据,如果不是,则给出全部数据
		if($action == 'index')
		{
			//截取数据
			$offset = ($this->p-1)*$this->pageSize;
			$userDatas = array_slice($userDatas, $offset , $this->pageSize);	
		}

		//传值 
		$this->cycleId = $cycleId;
		$this->totalTask = $totalTask;
		$this->totalScore = $totalScore;
		$this->totalDoneScore = $totalDoneScore;
		$this->totalCount = $totalCount;
		$this->cycles = $cycles;
		$this->users = $users;
		$this->userDatas = $userDatas;
		$this->lastCacheTime = $lastCacheTime;
		return ture;
	}
	/**
	 * 数据导出
	 */
	public function listDataExportAction()
	{
		try
		{
			if (PHP_SAPI == 'cli')
			{
				E("请通过浏览器方问该页");
			}

			//计算当前需要导出的数据
			if(!$this->index($action = 'dataExport'))
			{
				E("数据处理有误,请退出后重试,或联系系统管理员");
			}
			//初始化
			$objPHPExcel = new PHPExcelServer();

			//新建sheet,并将0号sheet设置为当前需要写入的sheet
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex(0);

			//设置文件名
			if( $userId = (int)I('get.user_id') )
			{
				$UserL = new UserLogic();
				$user = $UserL->getListById($userId);
				$userName = $user['name'];
			}
			else
			{
				$userName = "全部";
			}

			$cycleId = (int)I('get.cycle_id');
			$CycleL = new CycleLogic();
			$cycle = $CycleL->getListById($cycleId);
			$cycleName = $cycle['name'];

			$fileName = "教科研数据-" . $userName . "-" . $cycleName . "-" . date("YmdHi");
			$objPHPExcel->setFileName($fileName);

			//设置sheet 标题
			$CycleL = new CycleLogic();
			$cycle = $CycleL->getListById($this->cycleId);
			if(!$sheetTitle = $cycle['name'])
			{
				$sheetTitle = "mengyunzhi";
			}
			$objPHPExcel->setSheetTitle($sheetTitle);

			//设置标题
			$title = "天职师大经管学院绩效报表--教科研业绩";
			$objPHPExcel->setTitle($title);

			//设置副标题
			$subTitle = "统计日期:" . date("Y/m/d");
			$objPHPExcel->setSubTitle($subTitle);

			//设置header
			$header = array("姓名","预期得分","实际得分","基础科研任务","岗位科研任务","总任务","预期完成率%","实际完成率%",);
			$objPHPExcel->setHeader($header);

			//设置列宽
			$width = array(8,10,10,14,14,8,14,14);
			$objPHPExcel->setWidth($width);
			
			//设置数据
			$objPHPExcel->setDatas($this->userDatas);

			//设置数据及类型 ,类型string 字符串, int 整形 ,money以元为单位
			$key = array("user_name","score","doneScore","BaseTask","PostTask","totalTask","totalPercent","donePercent");
			$objPHPExcel->setKey($key);

			//type暂时还没有用到,用来格式化,当然了,最好是按要求的二维数组
			$type = array("string","int","int","int","int","int","int","int");
			$objPHPExcel->setType($type);

			//创建数据
			$objPHPExcel->create();

			//写入footer,取当前行号,并上移一行
			$row = $objPHPExcel->getRow() - 1;
			$beginRow = $objPHPExcel->getBeginRow();
			$endRow = $objPHPExcel->getEndRow();
			$colLetters = $objPHPExcel->getColLetters();

			//写入总计
			$activeSheet = $objPHPExcel->getActiveSheet();
			$activeSheet->setCellValue($colLetters[0] . $row , "总计:");

			//从第三列开始,到第7列,分别求和
			for($col++;$col<6;)
			{
				$col++;
				$activeSheet->setCellValue($colLetters[$col] . $row , "=sum(" . $colLetters[$col].$beginRow.":".$colLetters[$col].$endRow .")");
			}

			//计算完成率
			$col++;
			$activeSheet->setCellValue($colLetters[$col] . $row , "=".$colLetters[2]. $row . "*100/" .$colLetters[6]. $row . ")");
			$col++;
			$activeSheet->setCellValue($colLetters[$col] . $row , "=".$colLetters[3]. $row . "*100/" .$colLetters[6]. $row . ")");
	
			//下载下载格式
			$fileType = 'xls';
			$objPHPExcel->setFileType($fileType);

			//下载文件
			$objPHPExcel->download();
			exit();
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	/**
	 * 详情列表数据导出
	 * @return [type] [description]
	 */
	public function detailListDataExportAction()
	{
		$this->type = "ScientificResearch";
		$this->detailListDataExport();
	}
}