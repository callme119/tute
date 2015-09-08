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
use Department\Model\DepartmentModel;
class IndexController extends AdminController
{
    public function indexAction()
    {
        //获取部门列表
        $departmentModel = new DepartmentModel;
        $departmentTree = $departmentModel -> getDepartmentTree(0,2,'_son');
        $departmentList = tree_to_list($departmentTree,1,'_son','_level','order');

        //url信息
        $url=array("editDepart"=>U('editDepart'),"post"=>U('post'),"people"=>U('people'),"delete"=>U('delete'));
        $departmentList = add_url($departmentList,'_url',$url,'id');
        //传值
        $this->assign('departmentList',$departmentList);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);

    }
    public function addDepartAction(){
        $this->assign('css',$this->fetch("departCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    public function editDepartAction(){
        $this->assign('css',$this->fetch("departCss"));
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