<?php
/**
 * PHPEXECL
 */
namespace PHPExcel\Server;
Vendor('PHPExcel.PHPExcel');
class PHPExcelServer extends \PHPExcel
{	
	protected $fileType = 'xls';	//文件类型(xls,xlsx)	
	protected $fileName = "梦云智";
	protected $sheetTitle = "mengyunzhi";	//sheet标题
	protected $titel = "梦云智--做每天被梦想叫想的人"; //主标题
	protected $subTitle = "梦云智--重复的事情认真做";	//副标题
	protected $header = array();	//设置标题
	protected $width = array();		//设置每列的宽度
	protected $key = array();		//设置输出字段
	protected $type = array();		//输出字段的类型 
	protected $datas = array();		//主要输出内容
	protected $footer	= "梦云智--简单的事情重复做";	//页脚
	protected $error = "";			//报错信息
	protected $row = 1;		//记录当前行
	protected $col = 0;		//记录当前列
	protected $colLetters = array();	//用于将列转换为字母
	protected $firstColTitle = "序号";	//第一列标题
	protected $firstColWidth = 6;		//第一列列宽
	protected $copyRight = "Power By:梦云智";	//设置版权
	protected $beignRow;	//数据记录开始行
	protected $endRow;		//数据记录结束行

	public function getBeginRow()
	{
		return $this->beginRow;
	}
	public function getEndRow()
	{
		return $this->endRow;
	}		
	/**
	 * 行号表
	 * @return [type] [description]
	 */
	public function getColLetters()
	{
		return $this->colLetters;
	}

	/**
	 * 设置文件类型,是2007还是2003
	 * @param [type] $fileType [description]
	 */
	public function setFileType($fileType)
	{
		if($fileType == "xls" || $fileType == "xlsx")
		{
			$this->fileType = $fileType;
		}
	}

	/**
	 * 设置当前行
	 * @param int $row 当前行
	 */
	public function setRow($row)
	{
		$this->row = (int)$row;
	}

	/**
	 * 设置当前列
	 * @param int $col 当前列
	 */
	public function setCol($col)
	{
		$this->col = (int)$col;
	}

	//获取当前行
	public function getRow($row)
	{
		return $this->row;
	}	

	//获取当前列
	public function getCol($col)
	{
		return $this->col;
	}

	//设置标题
	public function setTitle($title)
	{
		$this->title = trim($title);
	}

	//设置副标题
	public function setSubTitle($subTitle)
	{
		$this->subTitle = trim($subTitle);
	}


	/**
	 * 设置 输出的字段 信息
	 * @param array $key 一组数组
	 */
	public function setKey($key)
	{
		if(!is_array($key))
		{
			$this->error = "the 'key' is not array";
			return false;
		}
		$this->key = $key;
	}

	/**
	 * 显示错误
	 * @return string 
	 */
	public function getError()
	{
		return $this->error;
	}

	/**
	 * 设置核心数据
	 * @param array $datas 二维数据
	 */
	public function setDatas($datas)
	{
		if(!is_array($datas))
		{
			$this->error = "the 'datas' is not array";
			return false;
		}
		$this->datas = $datas;
	}
	/**
	 * 设置标题
	 * @param array $header  一组数组
	 */
	public function setHeader($header)
	{
		if(!is_array($header))
		{
			$this->error = "the 'header' is not array";
			return false;
		}
		$this->header = $header;
	}
	/**
	 * 设置列宽
	 * @param array $width  一组数组
	 */
	public function setWidth($width)
	{
		if(!is_array($width))
		{
			$this->error = "the 'width' is not array";
			return false;
		}
		$this->width = $width;
	}

	/**
	 * 设置字段类型
	 * @param array $type  一组数组
	 */
	public function setType($type)
	{
		if(!is_array($type))
		{
			$this->error = "the 'type' is not array";
			return false;
		}
		$this->type = $type;
	}

	/**
	 * 设置当前活动sheet的标题
	 * @param string $sheetTitle  标题描述
	 */
	public function setSheetTitle($sheetTitle)
	{
		$this->sheetTitle = trim($sheetTitle);
	}
	

	/**
	 * 设置文件名
	 * @param string $fileName 文件名
	 */
	public function setFileName($fileName)
	{
		$this->fileName = trim($fileName);
	}
	public function __construct()
	{
		parent::__construct();
		$this->fileName = $this->fileName . (string)time();

		//设置公用属性
		$this->getProperties()->setCreator("www.mengyunzhi.com")
							 ->setLastModifiedBy("www.mengyunzhi.com")
							 ->setTitle("Power By:梦云智")
							 ->setSubject("Power By: ThinkPHP")
							 ->setDescription("Power By:PHPExcel")
							 ->setKeywords("梦云智 PHP THINKPHP PHPEXCEL")
							 ->setCategory("mengyunzhi");

		$char = "A";
		//设置字母表
		for($i = 0 ; $i < 26 ; $i++)
		{
			$this->colLetters[] = $char;
			$char = chr(ord($char)+1);
		}
	}
	/**
	 * 触发用户下载行为
	 * @return file xls或是xlsx文件
	 */
	public function create()
	{
		//判断headers width key的长度要求
		$length = count($this->header);

		//判断 header key 的长度
		if( $length != count($this->key) )
		{
			$this->error = "the length of 'key' and 'header' is not eq";
			return false;
		}

		//判断如果有width的长度
		if( !empty($this->width) && $length != count($this->width))
		{
			$this->error = "the length of 'width' and 'header' is not eq";
			return false;
		}

		//判断如果有type的长度
		if( !empty($this->type) && $length != count($this->type))
		{
			$this->error = "the length of 'type' and 'header' is not eq";
			return false;
		}

		//取活动sheet
		$activeSheet = $this->getActiveSheet();

		//写sheet标题
		$activeSheet->setTitle($this->sheetTitle);

		//写主标题,合并单元格,设置字体,居中
		$activeSheet->setCellValue($this->colLetters[$this->col].$this->row, $this->title);
		$activeSheet->getStyle($this->colLetters[$this->col].$this->row)->getFont()->setSize(16);
	    $activeSheet->getStyle($this->colLetters[$this->col].$this->row)->getFont()->setBold(true);
		$activeSheet->mergeCells($this->colLetters[$this->col].$this->row .":".$this->colLetters[($this->col+$length)].$this->row);
		$activeSheet->getStyle($this->colLetters[$this->col].$this->row .":".$this->colLetters[($this->col+$length)].$this->row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	
		$this->row++;

		//写副标题 ,合并单元格,设置字体,居中
		$activeSheet->setCellValue($this->colLetters[$this->col].$this->row, $this->subTitle);
		$activeSheet->getStyle($this->colLetters[$this->col].$this->row)->getFont()->setSize(14);
		$activeSheet->mergeCells($this->colLetters[$this->col].$this->row .":".$this->colLetters[($this->col+$length)].$this->row);
		$activeSheet->getStyle($this->colLetters[$this->col].$this->row .":".$this->colLetters[($this->col+$length)].$this->row)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->row++;

		//写第一列标题
		$activeSheet->setCellValue($this->colLetters[$this->col].$this->row, $this->firstColTitle);
		
		//写其它列表头及宽度
		foreach($this->header as $key => $value)
		{
			$this->col++;
			$activeSheet->setCellValue($this->colLetters[$this->col].$this->row, $value);
		}

		//设置标题边框,TODO:底纹,字体颜色(白)
		$activeSheet->getStyle($this->colLetters[0].$this->row.":".$this->colLetters[$this->col].$this->row)->getBorders()->getTop()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
		$activeSheet->getStyle($this->colLetters[0].$this->row.":".$this->colLetters[$this->col].$this->row)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);
		$activeSheet->getStyle($this->colLetters[0].$this->row.":".$this->colLetters[$this->col].$this->row)->getFont()->setBold(true);

		//记录数据开始行信息
		$this->beginRow = $this->row+1;

		//添加数据
		$i = 0;			//用于设置底纹和显示序号
		foreach($this->datas as $data)
		{
			$this->row++;
			$this->col = 0;
			$i++;
			//设置第一列
			$activeSheet->setCellValue($this->colLetters[$this->col].$this->row, $i);
			
			//设置其它列
			foreach($this->key as $key)
			{
				$this->col++;
				$activeSheet->setCellValue($this->colLetters[$this->col].$this->row, $data[$key]);
			}

			//偶数行设置底纹
			if($i % 2 == 0)
			{
				$activeSheet->getStyle($this->colLetters[0].$this->row.":".$this->colLetters[$this->col].$this->row)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID);  
				$activeSheet->getStyle($this->colLetters[0].$this->row.":".$this->colLetters[$this->col].$this->row)->getFill()->getStartColor()->setARGB('FFEEEEEE');
			}
		}

		//设置最后一行的下border
		$activeSheet->getStyle($this->colLetters[0].$this->row.":".$this->colLetters[$this->col].$this->row)->getBorders()->getBottom()->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

		//记录数据结束行信息
		$this->endRow = $this->row;

		//设置版权
		$this->row++;
		$this->row++;
		$this->col = 0;
		$activeSheet->setCellValue($this->colLetters[$this->col].$this->row, $this->copyRight);
			
		//设置列宽,第一列和其它几列分开设置
		$activeSheet->getColumnDimension($this->colLetters[$this->col])->setWidth($this->firstColWidth);
		$i = 0;
		foreach($this->width as $width)
		{
			$i++;
			$activeSheet->getColumnDimension($this->colLetters[$this->col+$i])->setWidth($width);
		}

		//设置水平居中
		$activeSheet->getStyle($this->colLetters[0] . $this->beginRow . ":" .$this->colLetters[0] . $this->endRow)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
	}

	/**
	 * 下载文件
	 * @return [type] [description]
	 */
	public function download()
	{
		if($this->fileType == 'xlsx')
		{
			// Redirect output to a client’s web browser (Excel2007)
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		}
		else
		{
			// Redirect output to a client’s web browser (Excel5)
			header('Content-Type: application/vnd.ms-excel');
		}
		header('Content-Disposition: attachment;filename="' . $this->fileName . '.' . $this->fileType. '"');
		header('Cache-Control: max-age=0');
		//针对IE 9浏览器做的优化
		// If you're serving to IE 9, then the following may be needed
		// header('Cache-Control: max-age=1');
		//用SSL 服务证书则需要将以下注释去掉
		// If you're serving to IE over SSL, then the following may be needed
		// header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		// header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		// header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		// header ('Pragma: public'); // HTTP/1.0
		if($this->fileType == 'xls')
		{
			$objWriter = IOFactoryServer::createWriter($this, 'Excel5');
		}
		else
		{
			$objWriter = IOFactoryServer::createWriter($this, 'Excel2007');
		}
		$objWriter->save('php://output');
	}

	//根据传入的数据导出excel
	public function index($data,$header,$letter){
		$excel = new \PHPExcel();
		$tableheader = $header;
		for($i = 0;$i < count($tableheader);$i++) {
			$excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
		}
		for ($i = 2;$i <= count($data) + 1;$i++) {
			$j = 0;
			foreach ($data[$i - 2] as $key=>$value) {
				$excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
			$j++;
			}
		}
		$write = new \PHPExcel_Writer_Excel5($excel);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
		header("Content-Type:application/force-download");
		header("Content-Type:application/vnd.ms-execl");
		header("Content-Type:application/octet-stream");
		header("Content-Type:application/download");
		header('Content-Disposition:attachment;filename="testdata.xls"');
		header("Content-Transfer-Encoding:binary");
		$write->save('php://output');
	}
}