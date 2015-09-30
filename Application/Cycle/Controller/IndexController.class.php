<?php
namespace Cycle\Controller;
use Admin\Controller\AdminController;
use Cycle\Logic\CycleLogic;				//周期
use Project\Logic\ProjectLogic;		//项目
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
		$this->assign('js',$this->fetch("indexJs"));
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
			
			$CycleL = new CycleLogic();
			//存在ID，即为编辑，取出当前周期数据
			if($id)
			{
				$cycle = $CycleL->getListById($id);
			}

			//取出所有父周期
			$rootLists = $CycleL->getRootLists();

			$this->assign("rootLists",$rootLists);
			$this->assign("cycle",$cycle);
			$this->assign('js',$this->fetch("editJs"));
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
			//先检查是否有项目已经添加到了这个周期上。
			$ProjectL = new ProjectLogic();
			$project = $ProjectL->getListsByCycleId($id);
			if(!empty($project))
			{
				$this->error("已经有项目添加到该周期上，请先修改或删除相关项目",U("index?p=" . I('get.p')));				
				return;
			}

			$CycleL->deleteById($id);
			$this->success("操作成功",U('index?p='.I('get.p')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}

	}

	/**
	 * 设某一周期 设置为当前统计周期
	 */
	public function setCurrentAction()
	{
		$id = (int)I('get.id');
		try
		{
			//取出当前记录，没有报错。
			$CycleL = new CycleLogic();
			if( !$cycle = $CycleL->getListById($id))
			{
				E("传入的ID值有误，未找到相关记录");
			}

			//删除原有的当前周期,实为更新操作
			$CycleL->deleteCurrentLists();

			//添加本条周期 
			$CycleL->setCurrentListById($id);

			$this->success("操作成功",U('index?p=' . I('get.p')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}

	}
}