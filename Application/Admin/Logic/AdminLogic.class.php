<?php

/*
 * 后台逻辑层，主要用来处理权限控制的相关逻辑
 * @author xuao
 * 295184686@qq.com
 */
namespace Admin\Logic;
use Think\Model;
use RoleUser\Model\RoleUserModel;
use RoleMenu\Logic\RoleMenuLogic;
use Menu\Model\MenuModel;
class AdminLogic{
	public function getPersonalMenuListByUserId($userId){
		//1通过用户id获取角色id信息
	            $roleUserModel = new RoleUserModel;
	            $roleIdList = $roleUserModel -> getRoleIdListByUserId($userId);

	            //2通过角色id获取角色对应的菜单id列表；
	            $roleMenuLogic = new RoleMenuLogic;
	            $menuIdList = $roleMenuLogic -> getMenuListByRoleList($roleIdList);

	            //3.获取相对应的左侧菜单树
	            $menuModel = new MenuModel();
	            if ($menuIdList) {
	                $where['id'] = array('in',$menuIdList);
	            }
	            $data = $menuModel -> getMenuTree(null,$where,1,2);

	            //返回菜单树给C层
            		return $data;
	}
	/**
	* 验证该用户是否有访问某一url权限
	* @param type $url 获取到的url信息
	* @param type $userId 用户id
	* @return boolean 是否有权限进行跳转
	* 2015年7月18日19:29:51
	*/
	public function checkUrl($url,$userId){
		if($url == null && $userId == null){
			return false;
		}else{
			//1通过用户id获取角色id信息
		            $roleUserModel = new RoleUserModel;
		            $roleIdList = $roleUserModel -> getRoleIdListByUserId($userId);

		            //2通过角色id获取角色对应的菜单id列表；
		            $roleMenuLogic = new RoleMenuLogic;
		            $menuIdList = $roleMenuLogic -> getMenuListByRoleList($roleIdList);
		            if(!$menuIdList){
			            	if(APP_DEBUG){
			            		$menuIdLists = array();
			            		$menuModel = new MenuModel;
			            		$menuIdList = $menuModel -> field('id') -> select();
			            		foreach ($menuIdList as $key => $value) {
			            			$menuIdLists[] = $value['id'];
			            		}
			            }
		            }
			$res = in_array($url['id'], $menuIdLists);
           			return $res;
		}
	   	
	}
}