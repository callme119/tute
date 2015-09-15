<?php
/**
 * 周期widget
 */
namespace Cycle\Widget;
use Think\Controller;
use Cycle\Logic\CycleLogic;		//周期
class CycleWidget extends Controller
{	
	public function getNamebyIdAction($id , $default = '根周期')
	{
		try
		{
			$CycleL = new CycleLogic();
			$cycle = $CycleL->getListById($id);

			return isset($cycle['name'])? $cycle['name'] : $default;
		}
		catch(\Think\Exception $e)
		{
			return "";
		}
	}
	
}