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

	public function index($type = 'index')
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

		//如果是ACTION则截取数据,如果不是,则给出全部数据
		if($type == 'index')
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
	}
	/**
	 * 数据导出
	 */
	public function DataExportAction()
	{
		try
		{
			if (PHP_SAPI == 'cli')
			{
				E("请通过浏览器方问该页");
			}

			$this->index($action = 'dataExport');

			//初始化,并将当有活动sheet给activeSheet
			$objPHPExcel = new PHPExcelServer();
			$activeSheet = $objPHPExcel->setActiveSheetIndex(0);

			//设置TH
			$headers = array(
				"序号",
				"姓名",
				"预期得分",
				"实际得分",
				"基础科研任务",
				"岗位科研任务",
				"总任务",
				"预期完成率%",
				"实际完成率%",
				);
			//设置宽度
			$headersWidth = array(4,6,8,8,12,12,6,12,12);

			$count = count($headers);


			//初始化行 列
			$col = 1;
			$beginRow = $row = 'A';


			
			//设置标题
			$CycleL = new CycleLogic();
			$cycle = $CycleL->getListById($this->cycleId);
			if(!$title = $cycle['name'])
			{
				$title = "mengyunzhi";
			}
			$activeSheet->setTitle($title);

			//设置居中
			//设置字体
			//合并单元格

			$endRow = chr(ord($row)+$count-1);
			$objPHPExcel->getActiveSheet()->mergeCells("$row$col:$endRow$col");

			//获取当前活动 sheet
			$activeSheet->setCellValue($row.$col, "天职师大经管学院绩效报表--教科研业绩");
			$col++;

			$objPHPExcel->getActiveSheet()->mergeCells("$row$col:$endRow$col");
			$activeSheet->setCellValue($row.$col, "统计日期:" . date("Y/m/d"));
			$col++;
			
			//输出TH	，给列宽	
			foreach($headers as $key => $header)
			{
				$activeSheet->setCellValue($row.$col, "$header");
				$objPHPExcel->getActiveSheet()->getColumnDimension($row)->setWidth($headersWidth[$key]);
				$row = chr(ord($row)+1);	//字符加1
			}

			//开始写数据
			$keys = array(
				"",
				"user_name",
				"score",
				"doneScore",
				"BaseTask",
				"PostTask",
				"totalTask",
				"totalPercent",
				"donePercent"
				);

			$row = $beginRow;
			$beginCol = ++$col;
			$i = 0;
			$j = 1;
			foreach($this->userDatas as $userData)
			{	
				$i = 0;
				foreach($keys as $key)
				{
					//首元素则直接给序号
					if( $i == 0)
					{
						$i++;
						$activeSheet->setCellValue("$row$col", "$j");
						$row = chr(ord($row)+1);
						continue;
					}
					$value = isset($userData[$key]) ? $userData[$key] : 0;
					$activeSheet->setCellValue("$row$col", "$value");
					$row = chr(ord($row)+1);
				}
				$endRow = chr(ord($row)-1);
				$row = $beginRow;
				$endCol = $col;
				$col++;
				$j++;
			}
			
			//记录结束单元格,用于统计
			
			$row = $beginRow;

			//总计:
			$activeSheet->setCellValue("$row$col", "总计:");
			$row = chr(ord($row)+2);

			//总分数\总任务求和
			$activeSheet->setCellValue("$row$col", "=sum($row$beginCol:$row$endCol)");
			$totalRow = $row;
			$row = chr(ord($row)+1);

			$activeSheet->setCellValue("$row$col", "=sum($row$beginCol:$row$endCol)");
			$doneRow = $row;	//记录预期分数的ROW
			$row = chr(ord($row)+1);

			$activeSheet->setCellValue("$row$col", "=sum($row$beginCol:$row$endCol)");
			$row = chr(ord($row)+1);	//记录已完成分数的ROW

			$activeSheet->setCellValue("$row$col", "=sum($row$beginCol:$row$endCol)");
			$row = chr(ord($row)+1);

			$activeSheet->setCellValue("$row$col", "=sum($row$beginCol:$row$endCol)");
			$totalTaskRow = $row;	//记录总的任务ROW
			$row = chr(ord($row)+1);

			//求完成率
			$activeSheet->setCellValue("$row$col", (int)ceil($this->totalScore*100/$this->totalTask));
			$row = chr(ord($row)+1);
			$activeSheet->setCellValue("$row$col", (int)ceil($this->totalDoneScore*100/$this->totalTask));
			$col++;
			$row = $beginRow;

			$objPHPExcel->getActiveSheet()->mergeCells("$row$col:$endRow$col");
			$activeSheet->setCellValue($row.$col, "Power By:梦云智");
			$col++;

			//设置格式,标题居中
			$activeSheet->getStyle("A1:I1")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$activeSheet->getStyle("A2:I2")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

			//设置标题与副标题字体
	        $objPHPExcel->getActiveSheet()->getStyle("A1:" . $endRow. "1")->getFont()->setSize(20);
	        $objPHPExcel->getActiveSheet()->getStyle("A1:" . $endRow. "1")->getFont()->setBold(true);
	        $objPHPExcel->getActiveSheet()->getStyle("A2:" . $endRow. "2")->getFont()->setSize(16);

	        //序号设置居中
	        //设置格式,第一列序号居中
			$activeSheet->getStyle("A$beginCol:A$endCol")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
			//设置边框
			//设置第一行单元格边框
        	$objPHPExcel->getActiveSheet()->getStyle("A".($beginCol-1) . ":$endRow".($beginCol-1))->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
        	$objPHPExcel->getActiveSheet()->getStyle("A".($beginCol-1) . ":$endRow".($beginCol-1))->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			
			//设置最后一行单元格边框
        	$objPHPExcel->getActiveSheet()->getStyle("A$endCol:$endRow$endCol")->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
			
			for($i=$beginCol+1; $i <= $endCol ; $i += 2)
			{
				//设置单元格边框
				//$objStyleA5->getFill();  
				$objPHPExcel->getActiveSheet()->getStyle("A$i:$endRow$i")->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);  
				$objPHPExcel->getActiveSheet()->getStyle("A$i:$endRow$i")->getFill()->getStartColor()->setARGB('FFEEEEEE');

				// $objPHPExcel->getActiveSheet()->getStyle("A".$beginCol+$i.":$endRow$endCol")->getStartColor()->setARGB('FFEEEEEE');
			}  
			$objPHPExcel->createSheet();
			$objPHPExcel->setActiveSheetIndex(1);
			$objPHPExcel->getActiveSheet()->setTitle('Simple1');
			// Set active sheet index to the first sheet, so Excel opens this as the first sheet
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->setFileType('xls');
			$objPHPExcel->download();
			// $objWriter = IOFactoryServer::createWriter($objPHPExcel, 'Excel2007');
			// // $objWriter->save('php://output');
			// $objWriter->save('php://output');
			exit;

		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
}