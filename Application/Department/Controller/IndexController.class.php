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
use Post\Model\PostModel;
use DepartmentPost\Model\DepartmentPostModel;
use UserDepartmentPost\Model\UserDepartmentPostModel;
class IndexController extends AdminController
{
    public function indexAction()
    {
        //获取部门列表
        $departmentModel = new DepartmentModel;
        $departmentList = $departmentModel -> getDepartmentList(0,2,'_son');
        //url信息
        $url=array("editDepart"=>U('editDepart',I('get.')),"post"=>U('post',I('get.')),"people"=>U('people'),"delete"=>U('delete',I('get.')));
        $departmentList = add_url($departmentList,'_url',$url,'id');
        //分页信息
        $this->assign('count',$departmentModel -> getTotalCount());
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
        $this->assign('js',$this->fetch("addDepartJs"));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //编辑部门
    public function editDepartAction(){
        //获取要编辑的部门id
        $id = I('get.id');
        $haveUsersPosts = array();
        $haveUsersPosts = $this->checkUserByDepartmentId($id);
        dump($haveUsersPosts);
        //获取要编辑的部门信息
        $departmentModel = new DepartmentModel;
        $departmentInfo = $departmentModel ->getDepartmentInfoById($id);
        //传值
        $this->assign('departmentInfo',$departmentInfo);

        //设置提交url
        $subUrl = U('update?id='.$id,I('get.'));
        $this->assign('subUrl',$subUrl);

        //将有用户的岗位编号数组传递到前台
        $this->assign('haveUsersPosts',$haveUsersPosts);

        //传递岗位列表和部门列表到前台
        $this->assign('departmentList',$this->_fetchDepartmentList());
        $this->assign('postList',$this->_fetchPostList());

        //传递部门-岗位信息到前台
        $departmentPostModel = new DepartmentPostModel;
        $departmentPostInfo = $departmentPostModel -> getDepartmentPostInfoByDepartId($id);
        $this->assign('departmentPostInfo',$departmentPostInfo);

        $this->assign('js',$this->fetch("addDepartJs"));
        $this->assign('css',$this->fetch("departCss"));
        $this->assign('YZBODY',$this->fetch('addDepart'));
        $this->display(YZTemplate);
    }  
    public function postAction(){
        $this->assign('url',U('postPeople'));
        $this->assign('YZBODY',$this->fetch());
        $this->display(YZTemplate);
    }
    //添加保存
    public function saveAction(){
        //添加部门信息
        $departmentModel = new DepartmentModel;
        $state = $departmentModel -> addDepartment();
        //添加部门-岗位信息
        $departmentPostModel = new DepartmentPostModel;
        $departmentPostModel->addDepartmentPost();
        
        if($state){
            $url = U('index');
            $this->success('保存成功',$url);
        }
    }
    //编辑保存
    public function updateAction(){

        //更新部门信息
        $departmentModel = new DepartmentModel;
        $state = $departmentModel -> updateDepartment();
        //更新部门-岗位信息
        $departmentPostModel = new DepartmentPostModel;
        $departmentPostModel->updataDepartmentPost();

        if($state){
            $url = U('index?p='.I('get.p'));
            $this->success('保存成功',$url);
        }
    }
    //删除保存
    public function deleteAction(){
        $id = I('get.id');
        //获取该部门下的岗位
        $departmentPostModel = new DepartmentPostModel;
        $departmentPostList = $departmentPostModel -> getDepartmentPostInfoByDepartId($id);
        
        $departmentPostIdList = array();
        foreach ($departmentPostList as $key => $value) {
            $departmentPostIdList[$key] = $value['id'];
        }

        $userDepartmentPostModel = new UserDepartmentPostModel;
        $hasUser = $userDepartmentPostModel -> getFirstListsByDepartmentPostIds($departmentPostIdList);
        if($hasUser){
            $url = U('index');
            $this->error('该部门关联的岗位中存在用户，如果直接删除会导致系统错误',$url);
        }
        //判断该部门是否存在下级部门
        //如果有就不能删除；如果没有，可以删除
        $departmentModel = new DepartmentModel;
        $hasChildDepartment = $departmentModel -> getDepartmentTree($id,1,null);

        if($hasChildDepartment){
            $url = U('index');
            $this->error('该部门中包含下级部门，如果直接删除会导致系统错误',$url);
        }else{
            $state = $departmentModel -> deleteDepartment();
        }
        if($state){
            $url = U('index');
            $this->success('删除成功',$url);
        }
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
        $postList = $postModel -> getAllLists();
        return $postList;
    }

    /*
     *  根据传入的部门Id判断该部门下哪些岗位仍有员工
     *  @return [array] [返回岗位列表]
     */
    public function checkUserByDepartmentId($department_Id){
        
        //根据岗位id取出部门岗位数组
        $departmentPostModel = new DepartmentPostModel;
        $departmentPostInfo = array();
        $departmentPostInfo = $departmentPostModel->getDepartmentPostInfoByDepartId($department_Id);

        //进行循环判断，将对应用户的部门岗位筛选出来
        $haveUsersPosts = array();
        $UserDepartmentPostModel = new UserDepartmentPostModel;
        foreach ($departmentPostInfo as $key => $value) {
            $users = $UserDepartmentPostModel->getListsByDepartmentPostId($value['id']);
            if(count($users) != 0){
                $haveUsersPosts[] = $value['post_id'];
             }
        }
        return $haveUsersPosts;
        

    }
}