<?php
/**
 * 考核周期
 */
namespace ScientificResearch\Widget;
use Think\Controller;
use Cycle\Logic\CycleLogic;		//考核周期
class CycleWidget extends Controller
{
	/**
	 * 通过ID值，获取该考核周期的名字，如果无此考核周期
	 * 输出默认值 
	 * @param  int $id          ID
	 * @param  string $defaultShow 默认输出
	 * @return string              名字
	 */
	public function getNameByIdAction($id , $defaultShow = '-')
	{
		$id = (int)$id;
		$CycleL = new CycleLogic();
		$cycle = $CycleL->getListById($id);
		if($cycle)
		{
			echo $cycle['name'];
		}
		else
		{
			echo $defaultShow;
		}
	}
}