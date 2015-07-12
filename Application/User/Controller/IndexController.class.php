<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
/**
 * Description of IndexController
 *
 * @author xuao
 */
namespace User\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController
{
    public function indexAction()
    {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addUserAction()
    {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function editUserAction()
    {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function reviseAction()
    {
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function successAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
}