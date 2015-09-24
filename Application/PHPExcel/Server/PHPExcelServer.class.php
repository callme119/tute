<?php
/**
 * PHPEXECL
 */
namespace PHPExcel\Server;
Vendor('PHPExcel.PHPExcel');
Vendor('PHPExcel.PHPExcel.Writer.Excel5');
vendor("PHPExcel.PHPExcel.IOFactory");
class PHPExcelServer extends \PHPExcel
{	

	/**
	 * 设置header, 设置后，触发用户下载行为.
	 */
	public function setHeader()
	{
		// Redirect output to a client’s web browser (Excel2007)
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="01simple.xlsx"');
		header('Cache-Control: max-age=0');

		//针对	做的优化
		// If you're serving to IE 9, then the following may be needed
		if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 9.0"))){
			header('Cache-Control: max-age=1');
		}

		//用SSL 服务证书则需要将以下注释去掉
		// If you're serving to IE over SSL, then the following may be needed
		// header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		// header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
		// header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		// header ('Pragma: public'); // HTTP/1.0
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