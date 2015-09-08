<?php

/*
 * 岗位对应的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Post\Model;
use Think\Model;
class PostModel extends Model{
/**
 * [getPostInfoById 通过id获取岗位信息]
 * @param  [type] $id [岗位id]
 * @return [type]     [岗位信息]
 */
    public function getPostInfoById($id) {
        $map['id'] = $id;
        $data = $this->where($map)->find();
        return $data;
    }
/**
 * [getPostList 获取岗位列表]
 * @return [type] [岗位列表]
 */
    public function getPostList(){
    	$data = $this -> select();
    	return $data;
    }
    public function addPost(){
    	$data = I('post.');
    	$this -> add($data);
    	return true;
    }

}