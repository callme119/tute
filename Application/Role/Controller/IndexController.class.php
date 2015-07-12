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
namespace Role\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController
{
    public function indexAction()
    {
        $url=array(
            "addRole"=>U('addRole'),
            "editRole"=>U('editRole'),
            "people"=>U('people'),
            );
        $this->assign('url',$url);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addRoleAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function peopleAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function editRoleAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
     public function saveOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    public function addOkAction(){
        $url = U('index');
        $this->success('保存成功',$url);
    }
    public function movePeopleOkAction(){
        $url = U('people');
        $this->success('保存成功',$url);
    }
    public function addPeopleOkAction(){
        $url = U('people');
        $this->success('保存成功',$url);
    }
    public function addPeopleAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
}
