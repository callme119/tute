<?php
/**
 * 数据模型基础表
 */
namespace DataModel\Controller;
use Admin\Controller\AdminController;
use DataModel\Model\DataModelModel;				//数据模型
use DataModel\Logic\DataModelLogic;				//数据模型
use DataModelDetail\Model\DataModelDetailModel;	//数据模型详情
use DataModelDetail\Logic\DataModelDetailLogic;	//数据模型详情
use ProjectCategory\Logic\ProjectCategoryLogic;	//类目类别
class IndexController extends AdminController{

	/**
	 * 列表初始化
	 * @return string 引用后台统一模板
	 * panjie 
	 * 3792535@qq.com
	 */
	public function indexAction()
	{
		$DataModelM = new DataModelModel();
		$dataModels = $DataModelM->getLists();

		$this->assign("totalCount",$DataModelM->getTotalCount());
		$this->assign("dataModels",$dataModels);
		$this->assign("YZBODY",$this->fetch());
		$this->display(YZTemplate);
	}

	/**
	 * 查询数据模型的详情列表
	 * @return [type] [description]
	 */
	public function detailAction()
	{
		$id = I('get.id');

		//取模型信息
		$DataModelM = new DataModelModel();
		$DataModel = $DataModelM->getListById($id);
		if($DataModel === false)
		{
			$this->error = $DataModelM->getError();
			$this->_empty();
		}

		//取模型详情
		$DataModelDetailL = new DataModelDetailLogic();
		$dataModelDetailRoots = $DataModelDetailL->getRootListsByDataModelId($id);
		if($dataModelDetailRoots === false)
		{
			$this->error = $DataModelDetailL->getError();
			$this->_empty();
		}

		//取当前模型记录对应的select子节点。
		$dataModelDetailSons = $DataModelDetailL->getSonListsByDataModelId($id);
		if($dataModelDetailSons === false)
		{
			$this->error = $DataModelDetailL->getError();
			$this->_empty();
		}

		$this->assign("DataModel",$DataModel);
		$this->assign("dataModelDetailRoots",$dataModelDetailRoots);
		$this->assign("dataModelDetailSons",$dataModelDetailSons);
		$this->assign("YZBODY",$this->fetch());
		$this->display(YZTemplate);
	}

	/**
	 * 编辑新的项目.
	 */

	public function editAction()
	{
		$id = I('get.id');

		//取模型信息
		$DataModelM = new DataModelModel();
		$dataModel = $DataModelM->getListById($id);

		$this->assign("dataModel",$dataModel);
		$this->assign("YZBODY",$this->fetch('edit'));
		$this->display(YZTemplate);
	}

	/**
	 * 保存
	 */
	public function saveAction()
	{
		$id = (int)I('post.id');
		$DataModelL = new DataModelLogic();
		try
		{
			//存在，执行更新操作
			if($id)
			{
				$DataModelL->savePost();
			}

			//不存在，执行插入操作
			else
			{
				unset($_POST['id']);
				$DataModelL->addPost();
			}
			$this->success("操作成功" , U('index?p='.I('get.p')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}	
	}

	/**
	 * 添加 
	 */
	public function addAction()
	{
		$this->editAction();
	}

	/**
	 * 删除
	 * TODO:返回值应该为AJAX
	 */
	public function deleteAction()
	{
		//检查该数据模型是否存在于 项目类别表中 已存在，则不能删
		$id = (int)I('get.id');
		if(!$id)
		{
			$this->error = "未接收到ID值";
			$this->_empty();
		}
		try
		{
			//查看项目模型是否被 项目类别 选中
			$ProjectCategoryL = new ProjectCategoryLogic();
			if( $ProjectCategoryL -> getListsByDataModelId($id))
			{
				E("已有项目类别绑定了该数据模型，请先删除相关项目类别");
			}

			//删除
			$DataModelL = new DataModelLogic();
			if( !$DataModelL->deleteById($id) )
			{
				E("传入的ID有误，或该条记录已被删除");
			}
			$this->success("操作成功" , U("index?p=" . I('get.p')));

		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}	
}