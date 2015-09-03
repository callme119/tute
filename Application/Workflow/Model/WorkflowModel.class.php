<?php

/*
 * 工作流的Model类
 *
 * @author denghaoyang
 * 275111108@qq.com
 */
namespace Workflow\Model;
use Think\Model;
class WorkflowModel extends Model{
	protected $error; //用于记录错误信息
	private function _setError($error)
	{
		$this->error = $error;
	}
	/**
	 * 能过传入的数组，获取当前相关数组的对应信息
	 * @param  [type] $lists   [传入包括当前数据表关键字的数组]
	 * @param  string $keyWord [当前数据表的关键字，在传入数组中的KEY值]
	 * @return [type]          [二维LIST数组]
	 */
	public function getListsByLists($lists , $keyWord = "workflow_id")
	{
		if(!is_array($lists))
		{
			$this->_setError("传入的数组格式不正确");
			return false;
		}
		$return = array();
		foreach($lists as $key => $value)
		{
			if(isset($value[$keyWord]))
			{
				$map['id'] = $value[$keyWord];
				$data = $this->where($map)->find();
				$return[$value[$keyWord]] = $data;
			}
			else
			{
				$this->_setError("传入的数组中，不包含keyword信息");
				return false;
			}
		}
		return $return;

	}
}