<?php
/**
 * 数据模型扩展
 */
 namespace DataModel\Controller;
 use DataModel\Logic\DataModelLogic;				//数据模型
 use DataModelDetail\Logic\DataModelDetailLogic;	//数据模型扩展
 use Admin\Controller\AdminController;				//后台基类
 class DataModelDetailController extends AdminController
 {
 	/**
 	 * 数据扩展信息编辑
 	 * @return [type] [description]
 	 */
 	public function editAction()
 	{
 		//取数据模型扩展信息
 		$dataModelDetailId = I('get.id');
 		$DataModelDetailL = new DataModelDetailLogic();
 		$dataModelDetail = $DataModelDetailL->getListById($dataModelDetailId);

 		//取数据模型信息
 		$dataModelId = $dataModelDetail['data_model_id'];
 		$DataModelL = new DataModelLogic();
 		$dataModel = $DataModelL->getListById($dataModelId);

 		//取同级数据扩展模型根信息
 		$dataModelDetailRoots = $DataModelDetailL->getRootListsByDataModelId($dataModelId);

 		$this->assign("dataModelDetailRoots",$dataModelDetailRoots);
 		$this->assign("dataModel",$dataModel);
 		$this->assign("dataModelDetail",$dataModelDetail);
 		$this->assign("YZBODY",$this->fetch('edit'));
 		$this->display(YZTemplate);
 	}

 	public function saveAction()
 	{
 		dump(I('post.'));
 		$id= (int)I('post.id');
 		try
 		{
 			$DataModelDetailL = new DataModelDetailLogic();

	 		//取ID值，有传入值，则执行添加操作。无值传入，执行更新操作。
	 		if($id)
	 		{
	 			$DataModelDetailL->savePostData();
	 		}

	 		//执行添加操作
	 		else
	 		{
	 			$DataModelDetailL->addPostData();
	 		}
	 		
	 		$this->success('',U('Index/detail?id=' . I('post.data_model_id')));
 		}
 		catch(\Think\Exception $e)
 		{
 			$this->error = $e;
 			$this->_empty();
 		}


 	}

 	public function addAction()
 	{
 		//取数据模型信息
 		$dataModelId = I('get.id');
 		$DataModelL = new DataModelLogic();
 		$dataModel = $DataModelL->getListById($dataModelId);

 		//取同级数据扩展模型根信息
 		$DataModelDetailL = new DataModelDetailLogic();
 		$dataModelDetailRoots = $DataModelDetailL->getRootListsByDataModelId($dataModelId);

 		$this->assign("dataModelDetailRoots",$dataModelDetailRoots);
 		$this->assign("dataModel",$dataModel);
 		$this->assign("dataModelDetail",$dataModelDetail);
 		$this->assign("YZBODY",$this->fetch('edit'));
 		$this->display(YZTemplate);
 	}
 }