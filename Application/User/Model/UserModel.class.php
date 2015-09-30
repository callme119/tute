<?php

/*
 * User用户Model
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace User\Model;
use Role\Model\RoleModel;
use Think\Model;
use Think\Model\RelationModel;
class UserModel extends RelationModel{
	protected $totalCount = 0; 	//记录总数

	protected $_link = array(
        		'Role'=>array(
		            'mapping_type'      => self::MANY_TO_MANY,
		            'class_name'        => 'Role',
		            'foreign_key'       =>  'user_id',
    			'relation_foreign_key'  =>  'role_id',
    			'relation_table'    =>  'yunzhi_user_role'
		            ),
        	);
	public function getTotalCount()
	{
		return $this->totalCount;
	}

	public function getAllName()
	{
		$res = $this->field('name,id')->select();
		return $res;
	}

	/**
	 * 规范化getUserById.为了保持程序向前兼容性。保留原函数
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function getListById($id)
	{
		return $this->getUserById($id);
	}
	/**
	 * 通过userid获取用户全部信息
	 * @param  [type] $userId [关键字]
	 * @return [type]         包括一个用户信息的数组
	 */
	public function getUserById($userId = null)
	{
		if($userId === null)
		{
			$this->error = "未传入userid";
			return false;
		}

		$map['id'] = $userId;
		return $this-> where($map)->find();
	}
	/**
	 * 通过包括有USERID的数组获取用户信息
	 * @param  array $lists   包括有userid项的数组
	 * @param  string $keyWord KEYWORD键值
	 * @return array          以userid为下标的数组
	 * panjie 3792535@qq.com
	 */
	public function getListsByLists($lists = null , $keyWord = "user_id" , $field = null )
	{
		if(!is_array($lists) || $lists === null)
		{
			$this->error = "传入的数据格式不正确";
			return false;
		}

		$return = array();
		foreach($lists as $key => $value)
		{
			if(!isset($value[$keyWord]))
			{
				$this->error = "关键字传入错误";
				return false;
			}
			$map['id'] = $value[$keyWord];
			if($field === null)
			{			
				$return[] = $this->where($map)->find();
			}
			else
			{
				$return[] = $this->field($field)->where($map)->find();
			}

		}
		return $return;
	}


	//获取教工列表
	public function getStaffList(){
		$data = $this ->relation(true)->select();
		//拼接教工的角色信息
		return $data;
	}
	//获取固定id的教工的信息
	public function getStaffById($id){
		$map['id'] = $id;
		$data = $this -> relation(true) ->where($map) ->find();
		return $data;
	}
	//添加教工
	public function addStaff(){
		$data['id'] = I('post.id');
		$data['username'] = I('post.username');
		$data['password'] = sha1(C(DEFAULT_PASSWORD));
		$data['name'] = I('post.name');
		$data['email'] = I('post.email');
		$id = $this->add($data);
		//post id给教工-部门岗位和教工-角色调用
		$_POST['id'] = $id;
		return true;
	}
	//编辑教工
	public function updateStaff(){
		//保存教工信息
		$data['email'] = I('post.email');
		$data['id'] = I('get.id');
		
		$this -> save($data);
		//post id给教工-部门岗位和教工-角色调用
		$_POST['id'] = $data['id'];
		return true;
	}
	public function deleteStaff(){
		$data['id'] = I('get.id');
		$state = $this -> where($data) ->delete();
		return $state;
	}
	/**
	 * [resetPassword 重置密码]
	 * 重置密码为mengyunzhi
	 * @param  [type] $userId [用户id]
	 * @return [type]         [description]
	 */
	public function resetPassword($userId){
		if ($userId == null) {
			$this ->error = "系统错误!";
			throw new \Think\Exception($this->error,1);
		}else{
			$data['id'] = $userId;
			$data['password'] = sha1(C(DEFAULT_PASSWORD));
			$this->save($data);
			return true;
		}
		
	}
	//将原密码sha1后与数据库进行对比
	public function checkPsw($oldpsw,$userId){
		$map = array();
		$map[id] = $userId;
		$info = $this->where($map)->find();
        		if(sha1($oldpsw) != $info['password']){
		            return 1;
		        }else{
		        	return 0;
		}
	}	

	public function changePhoneOrEmail($userId){
		$data = $this->create();
		$map = array();
		$map[id] = $userId;
		return $this->where($map)->save($data);
	}

	public function changePsw($newpsw,$userId){
		$data = array();
		$data['password'] = sha1($newpsw);
		$map = array();
		$map['id'] = $userId;
		return $this->where($map)->save($data);
	}

	//检查用户名与密码的正确性
	public function checkUser($username,$password){
		//根据用户名获取用户密码与用户信息
		$user = array();
		$user = $this->getUserInfoByName($username);
		if($user == null){
			return 2;//代表无此用户名
		}else if($user['password'] == sha1($password)){
			return 1;//代表验证成功
		}else{
			return 0;//代表验证失败
		}
	}

	//根据用户名取用户信息
	//$name string
	public function getUserInfoByName($name){
		$map = array();
		$map['username'] = $name;
		return $this->where($map)->find();
	}
}