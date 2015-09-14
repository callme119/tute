<?php
/**
 * dataModelDetailLogic 数据模型详情logic
 */
namespace DataModelDetail\Logic;
use DataModelDetail\Model\DataModelDetailModel;
class DataModelDetailLogic extends DataModelDetailModel
{
	/**
	 * 通过 数据模型ID 查找相关 根 记录
	 * @param  num $dataModelId 数据模型ID
	 * @return array     二维数组
	 */
	public function getRootListsByDataModelId($dataModelId , $map = array())
	{
		if(!is_numeric($dataModelId))
		{
			$this->error = "传入的ID值有误";
			return false;
		}

		$map[data_model_id] = $dataModelId;
		$map[pid]	= 0;
		$return = $this->where($map)->select();
		if(count($return) == 0)
		{
			$this->error = "未找到相关的记录，传入data_model值为$id";
		}
		return $return;
	}

	/**
	 * 查询所有的 儿子 结点信息.
	 * @param  num $dataModelId 数据模型id
	 * @return array              三维数据，以pid为键值返回
	 * panjie
	 * 3792535@qq.com
	 */
	public function getSonListsByDataModelId($dataModelId , $map = array())
	{
		if(!is_numeric($dataModelId))
		{
			$this->error = "传入的ID值有误";
			return false;
		}

		$map[data_model_id] = $dataModelId;
		$map[pid] = array("NEQ" , '0');
		$return = $this->where($map)->select();

		$data = array();
		foreach($return as $value)
		{
			$data[$value[pid]][] = $value;
		}
		return $data;
	}

/**
	 * 查询所有的 儿子 结点信息.
	 * @param  num $dataModelId 数据模型id
	 * @return array              二维数据，KEY值换为ID后，返回
	 * panjie
	 * 3792535@qq.com
	 */
	public function getSonListsArrayByDataModelId($dataModelId , $map = array())
	{
		if(!is_numeric($dataModelId))
		{
			$this->error = "传入的ID值有误";
			return false;
		}

		$map[data_model_id] = $dataModelId;
		$map[pid] = array("NEQ" , '0');
		$return = $this->where($map)->select();

		$data = array();
		foreach($return as $value)
		{
			$data[$value[id]] = $value;
		}
		return $data;
	}

	/**
	 * 保存POST提交数据
	 * @return [type] [description]
	 */
	public function savePostData()
	{
		$data = I('post.');
		$this->data($data)->save();
	}

	/**
	 * 新增POST数据 
	 */
	public function addPostData()
	{
		if($hello = $this->create())
		{
			$this->add();
		}
		else
		{
			E($this->getError());
		}

	}

	public function deleteById($id)
	{
		return $this->where("id = $id")->delete();
	}
}
