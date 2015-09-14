<?php
namespace Cycle\Controller;
use Admin\Controller\AdminController;
use Cycle\Logic\CycleLogic;				//周期
class IndexController extends AdminController
{
	public function indexAction()
	{
		//取列表，第一页数据和总个数
		$CycleL = new CycleLogic();
		$cycles = $CycleL->getLists();
		$totalCount = $CycleL->getTotalCount();

		$this->assign("cycles",$cycles);
		$this->assign("totalCount",$totalCount);
		$this->assign('YZBODY',$this->fetch());
		$this->display(YZTemplate);
	}

	public function editAction()
	{
		try
		{
			//初始化
			$id = (int)I('get.id');
			$cycle = null;

			//存在ID，即为编辑，取出当前周期数据
			if($id)
			{
				$CycleL = new CycleLogic();
				$cycle = getListById($id);
			}

			$this->assign('YZBODY',$this->fetch());
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
		$id = (int)I('post.id');
		try
		{
			$CycleL = new CycleLogic();
			//存在ID，则说明是更新操作
			if($id)
			{
				$_POST['id'] = $id;
				$CycleL->savePost();
			}

			//不存在ID，则说明是添加操作
			else
			{
				unset($_POST['id']);
				$CycleL->addPost();
			}
			$this->success("操作成功",U('index?p='.I('get.p')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
		

	}

	public function deleteAction()
	{
		$id = I('get.id');
		$CycleL = new CycleLogic();
		try
		{
			$CycleL->deleteById($id);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}

	}
}