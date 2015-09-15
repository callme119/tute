<?php
/**
 * 教学建设
 */
namespace ScientificResearch\Controller;
use Admin\Controller\AdminController;
class EducationController extends AdminController
{
	public function indexAction()
	{
		try
		{
			$IndexC = new IndexController();
			$IndexC->indexAction();
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
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
		try
		{
			$IndexC = new IndexController();
			$IndexC->detailAction();
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}
}
