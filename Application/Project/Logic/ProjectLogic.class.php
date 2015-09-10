<?php
/**
 * 项目表
 * author: panjie
 * 3792535@qq.com
 */
namespace Project\Logic;
use Project\Model\ProjectModel;
class ProjectLogic extends ProjectModel
{
	protected $tableName = null;	//数据表后缀

	//通过后缀设置数据表
	public function setTableSuffix($suffix)
	{
		$tableName = '__project_detail_' . $suffix . '__';
		$this->tableName = strtoupper($tableName);
	}

	//通过项目的详情ID来获取Lists数据
	public function getListByIdFromSuffixTable($projectDetailId)
	{
		if($this->tableName === null)
		{
			$this->error = "未传入数据表后缀";
			return false;
		}

		$map['id'] = $projectDetailId;
		return $this->table("$this->tableName")->where($map)->find();
	}
}