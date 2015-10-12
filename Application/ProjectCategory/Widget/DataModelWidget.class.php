<?php
/**
 * 数据模型表
 */
namespace ProjectCategory\Widget;
use Think\Controller;
use DataModel\Logic\DataModelLogic;			//数据模型
class DataModelWidget extends Controller
{
	public function getNamebyDataModelIdAction($DataModelId,$defaultShow = '-')
	{
		$DataModelL = new DataModelLogic();
		$list = $DataModelL->getListById($DataModelId);
		if($list)
		{
			echo $list['name'];
		}
		else
		{
			echo $defaultShow;
		}
		
	}
}