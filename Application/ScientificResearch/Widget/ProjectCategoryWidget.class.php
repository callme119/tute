<?php
/**
 * 项目类别widget
 */
namespace ScientificResearch\Widget;
use ProjectCategory\Model\ProjectCategoryModel;	//项目类别
use ProjectCategory\Logic\ProjectCategoryLogic;	//项目类别
use Think\Controller;

class ProjectCategoryWidget extends Controller
{
	public function getParentsDetailStringsByIdAction($id , $connector = "－〉")
	{
		$ProjectCategoryL = new ProjectCategoryLogic();
		$projectCategoryTree = $ProjectCategoryL->getTreeBySonId($id);

		$this->assign('projectCategoryTree',$projectCategoryTree);
		$this->assign('connector',$connector);
		$this->display('ProjectCategoryWidget/getParentsDetailStringsById');
	}
}