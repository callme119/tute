<?php
/**
 * 项目类别 logic
 */
namespace ProjectCategory\Logic;
use ProjectCategory\Model\ProjectCategoryModel;
class ProjectCategoryLogic
{
	/**
	 * 根据传入的子结点，返回所有的节点信息。
	 * @param  num $sonId 子节点信息
	 * @return array        二维数组
	 */
	public function getTreeBySonId($sonId)
	{
		//实例化
		$ProjectCategoryM = new ProjectCategoryModel();
		$return = array();
		$data = null;

		//先查找本结点，再查找上级结点。
		//当不存在结点信息时，退出。
		do{
			$return = $data;
			$map['id'] = $sonId;
			$data = $ProjectCategoryM->where($map)->find();
			$tem = $data;
			$sonId = $data['pid'];
			$data['_son'] = $return;
		}
		while($tem !== null );

		return $return;
	}
}