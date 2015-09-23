<?php
/**
 * 项目 widget
 */
namespace Myjob\Widget;
use Project\Logic\ProjectLogic;		//项目
class ProjectWidget
{
	public function getListJoinCagegoryByWorkflowIdAction($workflowId)
	{
		//取项目信息
		$ProjectL = new ProjectLogic();

		$map['w.id'] = $workflowId;

		$field['p.title'] = "name";
		$field['pc.name'] = "project_category_name";
		$field['p.user_id'] = "user_id";

		$ProjectL->alias("p");
		$return = $ProjectL->field($field)->join("left join __PROJECT_CATEGORY__ pc on p.project_category_id = pc.id left join __WORKFLOW__ w on p.id=w.project_id")->where($map)->find();
		// echo $ProjectL->getLastSql();
		return $return;

	}
}