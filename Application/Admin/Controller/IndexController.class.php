<?php
/*
 * 后台首页
 */
namespace Admin\Controller;
class IndexController extends AdminController
{
    public function indexAction()
    {
    	$this->assign('js',$this->fetch('indexJs'));
    	$this->assign('YZBODY',$this->fetch());
    	$this->display(YZTemplate);
    }
   
}

