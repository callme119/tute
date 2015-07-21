<?php
namespace Fail\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController
{
    /**
     * 初始化函数，404界面
     */
    public function failAction()
    {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
}