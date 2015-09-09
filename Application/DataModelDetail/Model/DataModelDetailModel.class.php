<?php
/**
 * 数据模型详情
 */
namespace DataModelDetail\Model;
use Think\Model;
class DataModelDetailModel extends Model
{
	protected $_auto = array(
		array('ratio',"100"),
		);
	protected $_validate = array();

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
}