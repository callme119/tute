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
namespace Department\Controller;
use Admin\Controller\AdminController;
class IndexController extends AdminController
{
    public function indexAction()
    {
        $url=array(
            "addDepart"=>U('addDepart'),
            "editDepart"=>U('editDepart'),
            "post"=>U('post'),
            "people"=>U('people'),
            );
        $this->assign('url',$url);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function addDepartAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function editDepartAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function peopleAction(){
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function postAction(){
        $this->assign('url',U('postPeople'));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function postPeopleAction(){
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
    public function movePostOkAction(){
        $url = U('post');
        $this->success('保存成功',$url);
    }
    public function addPostOkAction(){
        $url = U('post');
        $this->success('保存成功',$url);
    }
    public function addPostPeopleOkAction(){
        $url = U('postPeople');
        $this->success('保存成功',$url);
    }
    public function movePostPeopleOkAction(){
        $url = U('postPeople');
        $this->success('保存成功',$url);
    }
    public function movePeopleOkAction(){
        $url = U('people');
        $this->success('保存成功',$url);
    }
}

