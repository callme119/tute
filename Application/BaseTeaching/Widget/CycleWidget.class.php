<?php
/**
 * 考核周期 widget
 */
namespace BaseTeaching\Widget;
use Cycle\Logic\CycleLogic;		//考核周期
use Think\Controller;	//
class CycleWidget extends Controller
{
	public function getNameByIdAction($id , $defaultValue = '-')
	{
		$id = (int)$id;
		$CycleL = new CycleLogic();
		$cycle = $CycleL->getListById($id);
		echo isset($cycle['name']) ? $cycle['name'] : $defaultValue;
	}
}