<?php
/**
 * 项目模型
 */
namespace DataModel\Logic;
use DataModel\Model\DataModelModel;
class DataModelLogic extends DataModelModel
{
	/**
	 * 获取公共的模型字段列表
	 * @return [type] [description]
	 */
	public function getCommonLists()
	{
		$return = array(
			array("title"=>"名称", "name"=>"title" , "html_type" => "text" , "class" => ""),
			array("title" => "基础分值" , "name" => "sorce", "html_type" => "text" , "class"=> ""),
			array(
				"title"	=> "提交时间" , 
				"name"	=>	"time" , 
				"html_type" => "text" ,
				"class"=> "" ,
				"is_hidden" => 1),
			);
		return $return;
	}

	/**
	 * 添加POST信息
	 */
	public function addPost()
	{
		if($this->create())
		{
			$this->add();
		}
		else
		{
			E($this->getError());
		}
	}

	/**
	 * 保存POST信息
	 * @return [type]
	 */
	public function savePost()
	{
		$this->save(I('post.'));
	}

	public function deleteById($id)
	{
		return $this->where("id = $id")->delete();
	}
}
	