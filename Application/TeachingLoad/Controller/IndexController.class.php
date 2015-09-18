<?php
/*教学工作量模块
 * author:xulinjie
 * email:164408119@qq.com
 * create date:2015.07.15
 */
namespace TeachingLoad\Controller;
use Admin\Controller\AdminController;
use Cycle\Logic\CycleLogic;		//考核周期
class IndexController extends AdminController {
    public function indexAction(){
    	//取当前用户信息
    	$userId = get_user_id();

    	//取周期信息，按周期添加顺序排列
    	$CycleL = new CycleLogic();
    	$cycles = $CycleL->getLists();
    	$totalCount = $CycleL->getTotalCount();

    	//传值
    	$this->assign("totalCount",$totalCount);
		$this->assign("userId",$userId);
		$this->assign("cycles",$cycles);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
    //教学工作的添加
    public function addAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);  
    }
}