<?php
/**
 * 项目系数 设置表
 */
namespace ProjectCategoryRatio\Model;
use Think\Model;
class ProjectCategoryRatioModel extends Model
{
	public function getListsByProjectCategoryId($id)
	 {
	 	$map['project_category_id'] = $id;
	 	return $this->where($map)->select(); 
	 } 
}