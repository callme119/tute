<?php

/*
 * 公共项目表的Model类
 *
 * @author xulinjie
 * 164408119@qq.com
 */
namespace ProjectCategory\Model;
use Think\Model;
class ProjectCategoryModel extends Model{
	protected $_auto = array(
		);

	/**
	*初始化方法
	*/
	public function init()
	{
		$res = $this->where('pid=0')->select();
		return $res;
	}
	/*
	*保存
	*判断一下有没有相同的名称
	*/
	public function saveProject()
	{
		$data = I('post.');
		$id = $this->add($data);
		if($id)
        	return $id;
        else
        	return false;
	}
	/*
	*从数据库去数据，追加到页面
	*/
	public function append($pid)
	{

		$map['pid'] = $pid;
		$res = $this->where($map)->select();
		return $res;
	}
	/**
	 * 判断type的方法
	 */		
	public function getTypeById($id)
	{
		$map['id'] = $id;
		$res = $this->where($map)->field('type')->find();
		return $res;
	}

	/**
	 * 添加POST信息.名称为空，则报错，不为空，则添加。
	 */
	public function addListFromPost()
	{
		$post = I('post.');
		$type = $post['type'];
		switch ($type) {
			case 'Education':				//教学建设
				$data['is_education'] = 1;
				break;			
			case 'ServiceEducation':		//服务育人
				$data['is_service_education'] = 1;
				break;
			case 'Course':					//学科建设
				$data['is_course'] = 1;
				break;						//超额科研育人
			case 'Excess':
				$data['is_excess'] = 1;
				break;			
			default:
				$data['is_scientific'] = 1;	//教科研
				break;
		}
		$data['pid'] = !is_numeric($post['pid']) ? 0 : $post['pid'];
		$data['score'] = !is_numeric($post['score']) ? 0 : $post['score'];
		$data['data_model_id'] = !is_numeric($post['data_model_id']) ? 0 : $post['data_model_id'];
		$data['name'] = trim($post['name']);
		$data['is_team'] = $post['is_team'];
		if($data['name'] == '')
		{
			E("名称不能为空",1);
		}
		if($this->create($data))
		{
			return $this->add();
		}
	}

	public function saveListFromPost()
	{
		$post = I('post.');
		$type = $post['type'];
		switch ($type) {
			case 'Education':				//教学建设
				$data['is_education'] = 1;
				break;			
			case 'ServiceEducation':		//服务育人
				$data['is_service_education'] = 1;
				break;
			case 'Course':					//学科建设
				$data['is_course'] = 1;
				break;						//超额科研育人
			case 'Excess':
				$data['is_excess'] = 1;
				break;			
			default:
				$data['is_scientific'] = 1;	//教科研
				break;
		}

		$data['id'] = $post['id'];
		$data['pid'] = !is_numeric($post['pid']) ? 0 : $post['pid'];
		$data['score'] = !is_numeric($post['score']) ? 0 : $post['score'];
		$data['data_model_id'] = !is_numeric($post['data_model_id']) ? 0 : $post['data_model_id'];
		$data['name'] = trim($post['name']);
		$data['is_team'] = $post['is_team'];
		if($data['name'] == '')
		{
			E("名称不能为空",1);
		}
		if($this->data($data))
		{
			return $this->save();
		}
	}


	public function getListById($id)
	{
		$id = (int)$id;
		if($id)
			return $this->where("id = $id")->find();
		else
			return false;
	}
}