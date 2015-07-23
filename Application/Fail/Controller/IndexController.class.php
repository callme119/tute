<?php
namespace Fail\Controller;
use Admin\Controller\AdminController;
use Think\Controller;
class IndexController extends Controller
{
    /**
     * 初始化函数，404界面
     */
    public function failAction()
    {
        $this->display('fail'); 
    }
}