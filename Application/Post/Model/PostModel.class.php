<?php

/*
 * 岗位对应的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Post\Model;
use Think\Model;
use Think\Model\RelationModel;
class PostModel extends RelationModel{
    //关联Department表
            protected $_link = array(
                'Department'=>array(
                    'mapping_type'      => self::MANY_TO_MANY,
                    'class_name'        => 'Department',
                    'foreign_key'       =>  'post_id',
                    'relation_foreign_key'  =>  'department_id',
                    'relation_table'    =>  'yunzhi_department_post'
                    ),
            );
    //继承主类方法，添加totalCount值
    public function __construct(){
        parent::__construct();
        $this -> totalCount = count($this -> select());
    }
/**
 * [getPostInfoById 通过id获取岗位信息]
 * @param  [type] $id [岗位id]
 * @return [type]     [岗位信息]
 */
    public function getPostInfoById($id) {
        $map['id'] = $id;
        $data = $this->where($map) -> find();
        return $data;
    }
    public function deletePostById($id){
        /*delete方法的返回值是删除的记录数，
        如果返回值是false则表示SQL出错
        返回值如果为0表示没有删除任何数据。
        */
        //判断该岗位是否可以删除；如果该部门没有用户，可以 删除，
        //如果有，不可以删除
        $map['id'] = $id;
        $data = $this -> where($map) -> relation(true) ->delete();
        return $data;
    }
/**
 * [getPostList 获取岗位列表]
 * @return [type] [岗位列表]
 */
    public function getPostList(){
    	$data = $this -> page($this->p,$this->pageSize) -> select();
    	return $data;
    }
    /**
     * [getAllLists description]
     * @return [type] [description]
     */
    public function getAllLists(){
        $data = $this -> select();
        return $data;
    }
    
    public function addPost(){
    	$data = I('post.');
	$state = $this -> add($data);
	return $state;
    }
    public function updatePost(){
            $data = I('post.');
            $data['id'] = I('get.id');
            $state = $this -> save($data);
            if($state == 0){
                $state = true;
            }
            return $state;
    }

}