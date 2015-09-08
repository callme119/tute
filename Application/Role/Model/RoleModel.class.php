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
    /**
     * [updateRole 更新角色信息，把角色的基本信息添加到数据库中]
     * @return [boolean] 正确
     */
    public function updateRole(){
	$data = I('post.');
	//截取角色信息
	$roleData = array('id' =>I('get.id'),'name' => $data['name'],'remarks' => $data['remarks'],);
	$this->save($roleData);
	//给角色权限提供角色id
	$_POST['id'] = $roleData['id'];
	return true;
    }

    /**
     * [saveRole 添加角色数据]
     * @return [boolean] [添加成功]
     */
    public function saveRole(){
    	$data = I('post.');
    	$roleData = array('name' => $data['name'],'remarks' => $data['remarks'],);
	$id = $this->add($roleData);
	$_POST['id'] = $id;
	return true;
    }

    //根据用户编号取角色名称
    public function getRoleNameByUserId($id){
        $roleId = $this->getRoleById($id);
        $map = array();
        $map[id] = $id;
        $lists = $this->where($map)->find();
        return $lists[name];
    }
}