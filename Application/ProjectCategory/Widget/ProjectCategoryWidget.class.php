<?php
/**
 * ProjectCategoryWidget
 * 项目类别WIDGET
 * panjie
 * 2015/9.23
 */
namespace ProjectCategory\Widget;
use ProjectCategory\Model\ProjectCategoryModel;	//项目类别
use ProjectCategory\Logic\ProjectCategoryLogic;	//项目类别
use Think\Controller;
class ProjectCategoryWidget extends Controller
{
	/**
	 * 获取父级类别及自己类别的字符串
	 * @param  int $id        本身类别ID
	 * @param  string $connector 连接符号
	 * @return html            拼接好后送回
	 */
	public function getParentsDetailStringsByIdAction($id , $connector = "－〉")
	{
		$ProjectCategoryL = new ProjectCategoryLogic();
		$projectCategoryTree = $ProjectCategoryL->getTreeBySonId($id);

		$this->assign('projectCategoryTree',$projectCategoryTree);
		$this->assign('connector',$connector);
		$this->display(T('ProjectCategory@Widget/getParentsDetailStringsById'));
	}
}