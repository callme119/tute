<?php
/**
 * 项目扩展信息表
 */
namespace ProjectDetail\Model;
use Think\Model;
class ProjectDetailModel extends Model
{
	public function save($projectId,$dataModelDetailRoots)
	{
		$post = I('post.');
		foreach ($dataModelDetailRoots as $root) {
			$data['name'] = $root['name'];
			$data['project_id'] = $projectId;
			if($root[type]=='datatime'){
				$data['value'] = date_to_int($post[$root['name']]);
			}
			else{
				$data['value'] = $post[$root['name']];
			}
		}
		if($this->add($data)){
			return true;
		}
		else
			return false;
	}
}