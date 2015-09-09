<?php
/**
 * 数据模型基础表
 */
namespace DataModel\Controller;
use Admin\Controller\AdminController;
use DataModel\Model\DataModelModel;				//数据模型
use DataModelDetail\Model\DataModelDetailModel;	//数据模型详情
use DataModelDetail\Logic\DataModelDetailLogic;	//数据模型详情
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
		$DataModel = $DataModelM->getListById($id);
		if($DataModel === false)
		{
			$this->error = $DataModelM->getError();
			$this->_empty();
		}

		//获取模型公共列表
		$dataModelCommonLists = $DataModelM->getCommonLists();

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
		$this->assign("dataModelCommonLists",$dataModelCommonLists);
		$this->assign("dataModelDetailRoots",$dataModelDetailRoots);
		$this->assign("dataModelDetailSons",$dataModelDetailSons);
		$this->assign("YZBODY",$this->fetch());
		$this->display(YZTemplate);
	}
}