<?php
namespace Fail\Controller;
use Admin\Controller\AdminController;
use Think\Controller;
class IndexController extends AdminController
{
    /**
     * 初始化函数，404界面
     */
    public function failAction()
    {
    	$this->assign('url',U('Admin/Index/index'));
    	$this->assign('YZBODY',$this->fetch());
    	$this->display(YZTemplate);
       // $this->display('fail'); 
    }
}