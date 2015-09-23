<?php
/**
 * PHPEXECL
 */
namespace PHPExcel\Server;
Vendor('PHPExcel.PHPExcel');
Vendor('PHPExcel.PHPExcel.Writer.Excel5');
class PHPExcelServer extends \PHPExcel
{	
	//根据传入的数据导出excel
	public function index($data,$header,$letter){
		$excel = new \PHPExcel();
		$tableheader = $header;
		var_dump($data);
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