<?php
/**
 * 数据模型详情
 */
namespace DataModelDetail\Model;
use Think\Model;
class DataModelDetailModel extends Model
{
	protected $_auto = array(
		);
	protected $_validate = array(
		array('title','require','字段名不能为空')
		);

	/**
	 * 查找某条记录信息
	 * @param  numeric $id id
	 * @return array     一维数据
	 * panjie 
	 * 3792535@qq.com
	 */
	public function getListById($id)
	{
		if(!is_numeric($id))
		{
			$this->error = "未接到id值，或是接收的id值不正确";
			return false;
		}

		$map['id'] = $id;
		$return = $this->where($map)->find();
		if($return === null)
		{
			$this->error = "未找到接收ID值的相关记录";
			return false;
		}

		return $return;
	}

	/**
	 * 对过数据模型ＩＤ，获取所有扩展信息
	 * @param  int $dataModelId 数据模型ＩＤ
	 * @return array              二维数组，以关键字为ＫＥＹ
	 */
	public function getListsByDataModelId($dataModelId)
	{
		$data= $this->where("data_model_id = $dataModelId")->select();
		$return = array();
		foreach($data as $value)
		{
			$return[$value[id]] = $value;
		}
		return $return;
	}
}