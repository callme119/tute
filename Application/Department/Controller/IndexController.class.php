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
use Post\Modle\PostModel;
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
        //分页信息
        $this->assign('count',100);
        //传值
        $this->assign('departmentList',$departmentList);
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);

    }
    //添加部门
    public function addDepartAction(){
        //设置提交的url
        $subUrl = U('save');
        $this->assign('subUrl',$subUrl);

        //传递岗位列表和部门列表到前台
        $this->assign('departmentList',$this->_fetchDepartmentList());
        $this->assign('postList',$this->_fetchPostList());

        $this->assign('css',$this->fetch("departCss"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //编辑部门
    public function editDepartAction(){
        //获取要编辑的部门id
        $id = I('get.id');
        //获取要编辑的部门信息
        $departmentModel = new DepartmentModel;
        $departmentInfo = $departmentModel ->getDepartmentInfoById($id);
        //传值
        $this->assign('departmentInfo',$departmentInfo);

        //设置提交url
        $subUrl = U('update?id='.$id);
        $this->assign('subUrl',$subUrl);

        //传递岗位列表和部门列表到前台
        $this->assign('departmentList',$this->_fetchDepartmentList());
        $this->assign('postList',$this->_fetchPostList());
        
        $this->assign('css',$this->fetch("departCss"));
        $this->assign('YZBODY',$this->fetch('addDepart'));
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
    //添加保存
    public function saveAction(){
        $departmentModel = new DepartmentModel;
        $state = $departmentModel -> addDepartment();
        if($state){
            $url = U('index');
            $this->success('保存成功',$url);
        }
    }
    //编辑保存
    public function updateAction(){
        $departmentModel = new DepartmentModel;
        $state = $departmentModel -> updateDepartment();
        if($state){
            $url = U('index');
            $this->success('保存成功',$url);
        }
    }
    public function movePostAction(){
        $url = U('post');
        $this->success('保存成功',$url);
    }
    public function addPostAction(){
        $url = U('post');
        $this->success('保存成功',$url);
    }
    public function addPostPeopleAction(){
        $url = U('postPeople');
        $this->success('保存成功',$url);
    }
    public function movePostPeopleAction(){
        $url = U('postPeople');
        $this->success('保存成功',$url);
    }
    public function movePeopleAction(){
        $url = U('people');
        $this->success('保存成功',$url);
    }
    /**
     * [_fetchDepartmentList 传值到添加界面的部门列表]
     * @return [type] [返回部门列表]
     */
    private function _fetchDepartmentList(){
        $departmentModel = new DepartmentModel;
        $departmentTree = $departmentModel -> getDepartmentTree(0,2,'_son');
        $departmentList = tree_to_list($departmentTree,1,'_son','_level','order');
        return $departmentList;   
    }
    /**
     * [_fetchPostList 传值到添加界面的岗位列表]
     * @return [type] [返回岗位列表]
     */
    private function _fetchPostList(){
        $postModel = new PostModel;
        $postList = $postModel -> getPostList();
        return $postList;
    }
}