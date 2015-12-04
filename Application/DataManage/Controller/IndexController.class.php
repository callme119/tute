<?php
/**
 * 数据管理
 */
namespace DataManage\Controller;
use Admin\Controller\AdminController;
use DataManage\Logic\DataManageLogic;				//数据导出逻辑
use Cycle\Logic\CycleLogic;							//周期
use Task\Logic\TaskLogic;							//任务量
use User\Logic\UserLogic;							//用户
use Project\Logic\ProjectLogic;						//项目信息
use ProjectDetail\Logic\ProjectDetailLogic;			//项目扩展详情
use ProjectCategory\Logic\ProjectCategoryLogic;		//项目类别
use DataModel\Model\DataModelModel;					//数据模型
use DataModel\Logic\DataModelLogic;					//数据模型
use DataModelDetail\Model\DataModelDetailModel;		//数据模型扩展信息
use Workflow\Model\WorkflowModel;					//工作流
use Workflow\Logic\WorkflowLogic;					//工作流
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic;	//项目类别系数表
use WorkflowLog\Model\WorkflowLogModel;				//工作流详情
use Score\Logic\ScoreLogic;							//项目分值分布
use PHPExcel\Server\PHPExcelServer;					//PHPEXCEL 服务
class IndexController extends AdminController
{
	protected $cycles = array(); 			//考核周期
	protected $projects = array();			//用户考核数据
	protected $users = array();				//用户
	protected $cycleId = 0;					//当前周期
	protected $userDatas = array();			//用户数据
	protected $type;						//用户所点选的类型
	protected $userId;						//用户ID

	public function indexAction()
	{
		try
		{
			//初始化
			$this->index();
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

	/**
	 * 详情列表
	 * @return [type] [description]
	 */
	public function detailListAction()
	{
		try
		{
			$this->detailList();
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	/**
	 * 取详细列表信息,但是不显示
	 * 目标:使用列表显示与列表导出共用该方法
	 * 未涉及到分页,$action暂时不用
	 */

	public function detailList($action = "")
	{
		//取用户信息,周期信息
		$this->userId = $userId = (int)I("get.user_id");
		$this->cycleId = $cycleId = (int)I("get.cycle_id");
		$type = CONTROLLER_NAME;

		//取所有用户的列表
		$UserL = new UserLogic();
		$users = $UserL->getAllLists();

		//获取周期信息
		$CycleL = new CycleLogic();
		$cycles = $CycleL->getAllLists();

		//取当前周期下用户的项目信息
		$ScoreL = new ScoreLogic();

		//取当一用户
		if($userId)
		{
			$projects = $ScoreL->getListsByUserIdCycleIdType($userId , $cycleId , $type);
			$totalCount = $ScoreL->getTotalCount();
		}
		else
		{
			$projects = $ScoreL->getListsByCycleIdType($cycleId , $type);
			$totalCount = $ScoreL->getTotalCount();
		}

		$this->projects	= $projects;

		$this->assign("cycles",$cycles);			//全部周期信息
		$this->assign("users",$users);				//全部用户信息
		$this->assign("projects",$projects);		//项目信息
		$this->assign("totalCount",$totalCount);	//总条数
		$this->assign("js",$this->fetch('Index/detailListJs'));	//加载JS
		$this->assign("YZBODY",$this->fetch('Index/detailList'));
	}


	public function detailAction()
	{
		try
		{
			//取项目基础信息
            $projectId = I('get.id');
            $ProjectL = new ProjectLogic();
            if( !$project = $ProjectL->getListById($projectId) )
            {
                E("该记录不存在或已删除，传入projectId:$projectId");    
            }    

           	$this->assign("projectId",$projectId);
			$this->assign("YZBODY",$this->fetch('Index/detail'));
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
		
	}

	/**
	 * 列表数据导出
	 * @return [type] [description]
	 */
	public function listDataExport()
	{
		//初始化数据
		$this->index( $action = 'listDataExport');

		if (PHP_SAPI == 'cli')
		{
			E("请通过浏览器方问该页");
		}

		//初始化
		$objPHPExcel = new PHPExcelServer();

		//新建sheet,并将0号sheet设置为当前需要写入的sheet
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);

		//设置文件名
		if( $this->userId)
		{
			$UserL = new UserLogic();
			if(!$user = $UserL->getListById($this->userId))
			{
				E("传用的用户ID$userId有误");
			}
			$userName = $user['name'];
		}
		else
		{
			$userName = "全部";
		}

		$CycleL = new CycleLogic();
		if( !$cycle = $CycleL->getListById($this->cycleId))
		{
			E("传入的周期值".$this->cycleId."有误");
		}
		$cycleName = $cycle['name'];

		//判断是否是程序允许的类型 
		switch ($this->type) {
			case 'ServiceEducation':
				$typeName = "服务育人";
				break;		
			case "Course":
				$typeName = "学科业绩";
				break;	
			case "Excess":
				$typeName = "超额育人";
				break;	
			case "Education":
				$typeName = "教学建设";
				break;
			default:
				E("传入了错误的TYPE类型:" . $this->type);
				break;
		}
		$fileName = $typeName . "数据-". $userName . "-" . $cycleName . "-" . date("YmdHi");
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
		$title = "天职师大经管学院绩效报表--" . $typeName . "业绩";
		$objPHPExcel->setTitle($title);

		//设置副标题
		$subTitle = "统计日期:" . date("Y/m/d");
		$objPHPExcel->setSubTitle($subTitle);

		//设置header
		$header = array("姓名","预期得分","实际得分","任务分值","预期完成率%","实际完成率%",);
		$objPHPExcel->setHeader($header);

		//设置数据及类型 ,类型string 字符串, int 整形 ,money以元为单位
		$key = array("user_name","score","doneScore","task","totalPercent","donePercent");
		$objPHPExcel->setKey($key);

		//设置列宽
		$width = array(8,10,10,10,14,14);
		$objPHPExcel->setWidth($width);
		
		//设置数据
		$objPHPExcel->setDatas($this->userDatas);

		//type暂时还没有用到,用来格式化,当然了,最好是按要求的二维数组
		$type = array("string","int","int","int","int","int");
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
		for($col++;$col<4;)
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

		//计算完成率
		$col++;
		$activeSheet->setCellValue($colLetters[$col] . $row , "=".$colLetters[2]. $row . "*100/" .$colLetters[4]. $row . ")");
		$col++;
		$activeSheet->setCellValue($colLetters[$col] . $row , "=".$colLetters[3]. $row . "*100/" .$colLetters[4]. $row . ")");

		//下载下载格式
		$fileType = 'xls';
		$objPHPExcel->setFileType($fileType);

		//下载文件
		$objPHPExcel->download();
		exit();
	}

	/**
	 * 详情列表页数据导出
	 * @return [type] [description]
	 */
	public function detailListDataExport()
	{
		//数据初始化
		$this->detailList();

		$ProjectCategoryL = new ProjectCategoryLogic();
		$UserL = new UserLogic();
		$WorkflowL = new WorkflowLogic();
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();

		//添加其它信息
		foreach($this->projects as $key => $project)
		{
			//转换日期
			$this->projects[$key]['date'] = date("Y/m/d",$project['time']);

			//取类别信息
			$projectCategoryId = $project['project_category_id'];
			$projectCategoryChainName = $ProjectCategoryL->getTreeHtmlBySonId($projectCategoryId , $connecter = "->");
			$this->projects[$key]['project_category_chain_name'] = $projectCategoryChainName;

			//取用户信息
			$userId = $project['user_id'];
			$user = $UserL->getListById($userId);
			$userName = $user['name'];
			$this->projects[$key]['user_name'] = $userName;

			$userId = $project['commit_user_id'];
			$user = $UserL->getListById($userId);
			$userName = $user['name'];
			$this->projects[$key]['commit_user_name'] = $userName;

			//取审核状态
			$projectId = $project['id'];
			$workflow = $WorkflowL->getListByProjectId($projectId);
			$status = $workflow['is_finished'] ? "已审核" : "审核中";
			$this->projects[$key]['status'] = $status;

			//取分数
			$score = $ProjectCategoryRatioL->getScoreByProjectIdUserId($projectId,$userId);
			$this->projects[$key]['score'] = $score;
			$this->projects[$key]['donScore'] = $workflow['is_finished'] ? $score : 0;
		}

		//进行execl数据输出
		if (PHP_SAPI == 'cli')
		{
			E("请通过浏览器方问该页");
		}

		//初始化
		$objPHPExcel = new PHPExcelServer();

		//新建sheet,并将0号sheet设置为当前需要写入的sheet
		$objPHPExcel->createSheet();
		$objPHPExcel->setActiveSheetIndex(0);

		//设置文件名
		if( $this->userId)
		{
			$UserL = new UserLogic();
			if(!$user = $UserL->getListById($this->userId))
			{
				E("传用的用户ID$userId有误");
			}
			$userName = $user['name'];
		}
		else
		{
			$userName = "全部";
		}

		$CycleL = new CycleLogic();
		if( !$cycle = $CycleL->getListById($this->cycleId))
		{
			E("传入的周期值".$this->cycleId."有误");
		}
		$cycleName = $cycle['name'];

		//判断是否是程序允许的类型 
		switch ($this->type) {
			case 'ScientificResearch':
				$typeName = "教科研";
				break;
			case 'ServiceEducation':
				$typeName = "服务育人";
				break;		
			case "Course":
				$typeName = "学科业绩";
				break;	
			case "Excess":
				$typeName = "超额育人";
				break;	
			case "Education":
				$typeName = "教学建设";
				break;
			default:
				E("传入了错误的TYPE类型:" . $this->type);
				break;
		}
		$fileName = $typeName . "详情数据-". $userName . "-" . $cycleName . "-" . date("YmdHi");
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
		$title = "天职师大经管学院绩效报表--" . $typeName . "业绩详情";
		$objPHPExcel->setTitle($title);

		//设置副标题
		$subTitle = "统计日期:" . date("Y/m/d") . "  教工:" . $userName;
		$objPHPExcel->setSubTitle($subTitle);

		//设置header
		$header = array("名称","类别","提交时间","申请人","得分人","状态","分值","得分");
		$objPHPExcel->setHeader($header);

		//设置数据及类型 ,类型string 字符串, int 整形 ,money以元为单位
		$key = array("title","project_category_chain_name","date","commit_user_name","user_name","status","score","donScore");
		$objPHPExcel->setKey($key);

		//设置列宽
		$width = array(20,20,10,8,8,8,6,6);
		$objPHPExcel->setWidth($width);
		
		//设置数据
		$objPHPExcel->setDatas($this->projects);

		//type暂时还没有用到,用来格式化,当然了,最好是按要求的二维数组
		$type = array("string","string","date","string","string","string","int","int");
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

		//如果有数据则写入总和,没有，则给0
		//从第7列开始,到第8列,分别求和
		for($col =6;$col<8;)
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

		//下载下载格式
		$fileType = 'xls';
		$objPHPExcel->setFileType($fileType);

		//下载文件
		$objPHPExcel->download();
		exit();
	}

	/**
	 * 数据初始化
	 * @param  string $action 看是否由 数据导出 发起.
	 * 是:则不进行分页 否:则进行分页
	 * @return [type]         [description]
	 */
	public function index($action = "")
	{
		$this->init();
		$cycleId = $this->cycleId;
		$cycles = $this->cycles;			//取考核周期数据
		$projects = $this->projects;	//取用户项目得分信息
		$users = $this->users;				//用户信息

		//初始化用户数据
		$userDatas = array();

		//取个人得分
		$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
		foreach($projects as $key => $project)
		{
			
			$score = $ProjectCategoryRatioL->getScoreByProjectIdUserId($project['id'], $project['user_id']);

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
		foreach($userDatas as $key => $value)
		{
			$userId = $key;
			$value['user_id'] = $key;

			$type = CONTROLLER_NAME; //查基础科研任务
			$task = $TaskL->getListByUserIdCycleIdType($userId , $cycleId , $type);

			$value["task"] = (int)$task['value'];
			
			//总完成率
			$value['donePercent'] = (int)floor($value["doneScore"]*100/$value["task"]+0.5);

			//总预期完成率
			$value['totalPercent'] = (int)floor($value["score"]*100/$value["task"]+0.5);

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
		$this->userId = $userId;

		//看是否为数据导出,数据导出,则不进行数据截取
		if( $action != 'listDataExport')
		{
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
		}
		
		//传值
		$this->cycleId = $cycleId;
		$this->userDatas = $userDatas;

		$this->assign("cycleId",$cycleId);
		$this->assign("totalCount",$totalCount);
		$this->assign("cycles",$cycles);	//考核周期数据
		$this->assign("users",$users);		//用户数据
		$this->assign("userDatas",$userDatas);//用户数据信息
	}
}