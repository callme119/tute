<?php
/**
 * 数据管理－－教学建设数据
 * panjie
 * 3792535
 * 2015.9.22
 */
namespace DataManage\Controller;
class EducationController extends IndexController
{
	public function indexAction()
	{
		try
		{
			parent::indexAction();
			$cycleId = $this->cycleId;
			$cycles = $this->cycles;			//取考核周期数据
			$dataManages = $this->dataManages;	//取用户项目得分信息
			$users = $this->users;				//用户信息

			$this->assign("js",$this->fetch('Index/indexJs'));
			$this->assign("YZBODY",$this->fetch('Index/index'));
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	/**
	 * *列表数据导出
	 * @return [type] [description]
	 */
	public function listDataExportAction()
	{
		$this->type = "Education"; //设置类型
		$this->listDataExport();
	}
}