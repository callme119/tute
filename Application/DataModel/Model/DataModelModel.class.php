<?php
/**
 * 数据模型 模型层
 */
namespace DataModel\Model;
use Think\Model;
class DataModelModel extends Model
{
	protected $_auto = array(
		);
	protected $_validate = array(
		);
	/**
	 * 获取当前页的列表
	 * @return array 二维数组
	 */
	public function getLists()
	{
		$this->totalCount = $this->count();
		return $this->page($this->p,$this->pageSize)->select();
	}

	/**
	 * 获取状态为正常的数据模型列表 
	 * @return array 二维数组
	 */
	public function getNormalLists()
	{
		$map[state] = 0;
		return $this->where($map)->select();
	}
	/**
	 * 通过ID找相关记录
	 * @param  num $id KEY
	 * @return array     一维数组
	 */
	public function getListById($id)
	{
		$map[id] = $id;
		$return = $this->where($map)->find();
		if($return == null)
		{
			$this->error = "无数据记录";
			return false;
		}

		return $return;
	}

	/**
	 * 获取公共的模型字段列表
	 * @return [type] [description]
	 */
	public function getCommonLists()
	{
		$return = array(
			array("title"=>"名称", "name"=>"title" , "html_type" => "text" , "class" => ""),
			array("title" => "基础分值" , "name" => "sorce", "html_type" => "text" , "class"=> ""),
			);
		return $return;
	}
}