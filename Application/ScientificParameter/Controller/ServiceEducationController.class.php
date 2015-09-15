<?php
/**
 * 服务育人controller
 */
namespace ScientificParameter\Controller;
use ProjectCategory\Logic\ProjectCategoryLogic;	//项目类别
use Admin\Controller\AdminController;
class ServiceEducationController extends AdminController
{
	public function indexAction()
	{
		try
		{
			//取类别数据,有深度，所以取的是树
		    $ProjectCategoryL = new ProjectCategoryLogic();
		    $projectCategoryTree = $ProjectCategoryL->getSonsTreeById(0);

		    //树变数组
		    $projectCategoryLists = tree_to_list($projectCategoryTree ,$i = 0, '_son');

		    //取分页数据
		    $ProjectCategoryL = new ProjectCategoryLogic();
		    $lists = $ProjectCategoryL->getCurrentLists($projectCategoryLists);

		    //传值
		    $this->assign("totalCount",count($projectCategoryLists));
		    $this->assign("lists",$lists);
			$this->assign("YZBODY",$this->fetch());
			$this->display(YZTemplate);
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
			$this->assign("YZBODY",$this->fetch('add'));
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
}