<?php

/*
 * 项目细节的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace PublicProjectDetail\Model;
use Think\Model;
class PublicProjectDetailModel extends Model{
	
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

    }