<?php

/*
 * 角色的Model类
 *
 * @author xuao
 * 295184686@qq.com
 */
namespace Role\Model;
use Think\Model;
class RoleModel extends Model{
    /**
     * 获取角色列表方法
     * 
     * @return array
     */
    public function getRoleList($page){
    	$res = $this->page($page,C(PAGE_SIZE))->select();
    	return $res;
    }
    /**
     * [getRoleById description] 通过id获取角色信息
     * @param  [type] $id [description] 角色id
     * @return [type]     [description] 角色信息
     */
    public function getRoleById($id){
    	$map['id'] = $id;
    	$res = $this->where($map)->find();
    	return $res;
    }
}