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

	/**
	 * 通过ID获取当前ID的基本信息
	 * @param  sting $id 关键字
	 * @return [array]     [返回一条基本信息]
	 */
	public function getListById($id = null)
	{
		if($id === null || !is_string($id))
		{
			$this->error = "未传入ID值,或传入的ID值有误";
		}
		$map[id] = $id;
		$return = $this->where($map)->find();
		return $return;
	}

	/**
	 * 更新上一个审核链信息
	 * @param    int                   $id         
	 * @param    integer                  $preChainId 审核链ID
	 * @return                                  
	 * @author 梦云智 http://www.mengyunzhi.com
	 * @DateTime 2016-12-08T10:59:50+0800
	 */
	public function updatePreChainIdById($id, $preChainId = 0) {
		$data = array('id'=> $id, 'pre_chain_id' => $preChainId);
		if (!$this->create($data)) {
			$this->error = "数据创建错误，信息：" . $this->getError();
			return false;
		}
		$this->save();
	}

	/**
	 * 将审核链回跳到上一个结点
	 * @param    int                   $id KEY
	 * @return                          
	 * @author 梦云智 http://www.mengyunzhi.com
	 * @DateTime 2016-12-08T12:22:01+0800
	 */
	public function backToPreChainId($id) {
		$data = $this->getListById($id);
		$data['chain_id'] = $data['pre_chain_id'];
		if (!$this->create($data)) {
			return false;
		}
		$this->save();
	}
}