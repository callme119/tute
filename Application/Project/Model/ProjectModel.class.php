<?php

/*
 * 项目细节的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Project\Model;
use Think\Model;
class ProjectModel extends Model{
	
    private $order = array("time"=>"desc");

	//再取项目细节信息
    public function getListsByIds($lists){
    	$array = array();
    	foreach ($lists as $key => $value) {
    		$map = array();
    		$map['id'] = $value['public_project_detail_id'];
    		$array[$value['public_project_detail_id']] = $this->where($map)->find();
    	}
    	return $array;
    }

    /**
     * 获取项目列表
     * $this->p；当前页
     * $this->pageSize;当前页码大小
     * @return array 二维数组
     */
    public function getLists()
    {
        $map = array();
        $this->totalCount = $this->where($map)->count();
        return $this->where($map)->page($this->p,$this->pageSize)->order($this->order)->select();
    }

    public function getListsByUserId($userId)
    {
        $map = array();
        $map['user_id'] = $userId;
        $this->totalCount = $this->where($map)->count();
        return $this->where($map)->page($this->p,$this->pageSize)->order($this->order)->select();

    }
}