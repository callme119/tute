<?php
/**
 * 服务育人
 */
namespace ScientificResearch\Controller;
use Admin\Controller\AdminController;
class ServiceEducationController extends AdminController
{  
	public function indexAction()
	{
		$IndexC = new IndexController();
		$IndexC->indexAction();
	}

	public function addAction()
	{
		try
		{
			$IndexC = new IndexController();
			$IndexC->addAction();
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

	public function detailAction()
	{
		$IndexC = new IndexController();
		$IndexC->detailAction();
	}
}