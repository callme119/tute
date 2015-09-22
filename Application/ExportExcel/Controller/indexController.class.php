<?php
/**
 * excel导出模块
 */

namespace ExportExcel\Controller;
use Admin\Controller\AdminController;

class IndexController extends AdminController{
	
	/**
 	* 导出Excel报表
 	*  @param  array $header excel的表头数组  
 	*  @param  array $data excel的数据数组 二维数组
 	*/
	public function indexAction($header,$data){
		Vendor('Classes.PHPExcel');
        $excel = new \PHPExcel();
        $letter = array('A','B','C','D','E','F','F','G');
        //表头数组
        $tableheader = $header;
        //填充表头信息           
        for($i = 0;$i < count($tableheader);$i++) {
            $excel->getActiveSheet()->setCellValue("$letter[$i]1","$tableheader[$i]");
        }
        //表格数组
        $data = $data;
        //填充表格信息
        for ($i = 2;$i <= count($data) + 1;$i++) {
            $j = 0;
            foreach ($data[$i - 2] as $key=>$value) {
                $excel->getActiveSheet()->setCellValue("$letter[$j]$i","$value");
                $j++;
            }
        }
        //创建Excel输入对象
        Vendor('Classes.PHPExcel.Writer.Excel5');
        $write = new \PHPExcel_Writer_Excel5($excel);
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control:must-revalidate, post-check=0, pre-check=0");
        header("Content-Type:application/force-download");
        header("Content-Type:application/vnd.ms-execl");
        header("Content-Type:application/octet-stream");
        header("Content-Type:application/download");;
        header('Content-Disposition:attachment;filename="testdata.xls"');
        header("Content-Transfer-Encoding:binary");
        $write->save('php://output');
	}
}