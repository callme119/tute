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
	}
	/**
	 * 触发用户下载行为
	 * @return file xls或是xlsx文件
	 */
	public function download()
	{
		$this->getProperties()->setCreator("www.mengyunzhi.com")
							 ->setLastModifiedBy("www.mengyunzhi.com")
							 ->setTitle("Power By:梦云智")
							 ->setSubject("Power By: ThinkPHP")
							 ->setDescription("Power By:PHPExcel")
							 ->setKeywords("梦云智 PHP THINKPHP PHPEXCEL")
							 ->setCategory("mengyunzhi");


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