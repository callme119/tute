<?php
/**
 * 类别管理 
 */
namespace ProjectCategory\Controller;
use Admin\Controller\AdminController;
use ProjectCategory\Model\ProjectCategoryModel;
use DataModel\Model\DataModelModel;               //数据模型
use ProjectCategory\Logic\ProjectCategoryLogic;   //项目类别
use DataModelDetail\Model\DataModelDetailModel;
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic; //项目类别系数表
use Project\Logic\ProjectLogic;      
class IndexController extends AdminController
{
	public function indexAction()
	{
		try
		{    
			$type = CONTROLLER_NAME;
			$ProjectCategoryL = new ProjectCategoryLogic();
		    $projectCategoryTree = $ProjectCategoryL->getSonsTreeById(0 , $type);

		    //树变数组
		    $projectCategoryLists = tree_to_list($projectCategoryTree ,$i = 0, '_son');

		    //取分页数据
		    $ProjectCategoryL = new ProjectCategoryLogic();
		    $lists = $ProjectCategoryL->getCurrentLists($projectCategoryLists);

		    //传值
		    $this->assign("totalCount",count($projectCategoryLists));
		    $this->assign("lists",$lists);
			$this->assign("YZBODY",$this->fetch("Index/index"));
			$this->display(YZTemplate);
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	//管理员进行公共项目添加的界面
	public function addAction($projectDetail = null) {

		$ProjectCategoryL = new ProjectCategoryLogic();
		$id = 0;

		$type = CONTROLLER_NAME;
		$projectCategoryTree = $ProjectCategoryL->getSonsTreeById($id , $type);
		$projectCategory = tree_to_list($projectCategoryTree , $id , '_son' );

		//取数据模型列表
		$dataModelM = new DataModelModel();
		$dataModels = $dataModelM->getNormalLists();

		$this->assign("projectDetail",$projectDetail);
		$this->assign("js",$this->fetch("Index/addJs"));
		$this->assign("dataModels",$dataModels);
		$this->assign('projectCategory',$projectCategory);
		$this->assign('YZBODY',$this->fetch('Index/add'));
		$this->display(YZTemplate);
	}

	public function editAction()
	{
		$projectCategoryId = I('get.id');
		$ProjectCategoryL = new ProjectCategoryLogic();
		if(!$projectCategory = $ProjectCategoryL->getListById($projectCategoryId))
		{
		  $this->error = "传入了错误的ID值";
		  $this->_empty();
		}

		$this->addAction($projectCategory);
	}

	public function saveAction()
	{
		try
		{
			$ProjectCategoryM = new ProjectCategoryModel();
			$ProjectCategoryRatioL = new ProjectCategoryRatioLogic();

			//看是否有传入值。有传入值，则是更新操作。
			$projectCategoryId = I('post.id');
			$_POST['type'] = CONTROLLER_NAME;

			if( $projectCategory = $ProjectCategoryM->getListById($projectCategoryId))
			{
				//更新项目类别信息
				$ProjectCategoryM->saveListFromPost();

				//删除项目类别系数信息
				$ProjectCategoryRatioL->deleteListsByProjectCategoryId($projectCategoryId);
			}
			else
			{
				//没传入值，则是添加操作
				//添加项目类别信息
				$projectCategoryId = $ProjectCategoryM->addListFromPost();
				// dump($projectCategoryId);

			}

			//添加项目类别系数信息
			$dataModelDeatailIds = I('post.data_model_detail_id');
			$ProjectCategoryRatioL->addListsByProjectCategoryIdDataModelDetailIds($projectCategoryId,$dataModelDeatailIds);

			$this->success("操作成功",U('index' ,I('get.')));
		}
		catch(\Think\Exception $e)
		{
			$this->error = $e;
			$this->_empty();
		}
	}

	/**
 *  通过js传过来id，追加select的内容
 *  1.判断穿过来的id的type是否为0（如果为0还有子项目）
 *  2.如果type为0，pid=id取库 
 */
public function appendAction()
{
  $return = array('status' =>"success" ,'data'=>"" );
  $pid = I('get.id');

  $ProjectCategoryM = new ProjectCategoryModel();
  $res = $ProjectCategoryM->append($pid);
  $this->assign('data',$res);
  $return['data'] = $this->fetch();
  //echo $data;
  $this->ajaxReturn($return);
  
}
  /**
   * 返回项目模型的字段设置信息
   * @return array 二维数组
   * panjie
   * 3792535@qq.com
   */
  public function getDataModelDetailAjaxAction()
  {
    try
    {
      //取相关信息
      $dataModelId = I('get.datamodelid');
      $dataModelDetailM = new DataModelDetailModel();
      $dataModelDetails = $dataModelDetailM->where("data_model_id = $dataModelId")->select();

      $projectCategoryId = I('get.projectcategoryid');
      if($projectCategoryId != '0')
      {
          //获取当前 项目类别 的系数信息
        
          //返回原有系数信息,返回以data_model_detail_id为KEY值的信息
          $ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
          $projectCategoryRatios = $ProjectCategoryRatioL->getListsByProjectCategoryId($projectCategoryId);
          
          //依次加入 系数 
          foreach($dataModelDetails as $key => $dataModelDetail)
          {
              $dataModelDetails[$key][_ratio] = $projectCategoryRatios[$dataModelDetail[id]][ratio];
          } 
      }
      $return['dataModelDetails'] = $dataModelDetails;
      $return['state'] = success;
      return $this->ajaxReturn($return);

    }
    catch(\Think\Exception $e)
    {
      $return['state'] = error;
      $return['message'] = "发生异常，报错信息为：" . $e->getMessage();
      return $this->ajaxReturn($return);
    }
    
  }

  public function deleteAction()
  {
      //判断是否有子类别，有子类别不能删。
      $projectCategoryId = I('get.id');
      $ProjectCategoryL = new ProjectCategoryLogic();
      $return = $ProjectCategoryL -> getListsByPid($projectCategoryId);
      if( !empty($return)) 
      {
          $this->error("该项目结点存在子结点，请先删子结点。", U('index?p=' . I('get.p')));
          return;
      }

      //查看项目表中，是否有项目的类别已选该类别，已选则不能删。
      $ProjectL = new ProjectLogic();
      $return = $ProjectL -> getListsByProjectCategoryId($projectCategoryId);
      if(!empty($return))
      {
          $this->error("已有项目信息建立在该类别上，请先删除相关项目信息",U('index?p=' . I('get.p')));
          return;
      }

      //删除
      $map = array();
      $map['id'] = $projectCategoryId;
      $ProjectCategoryL ->where($map)-> delete();

      //删除类别系数表
      $map = array();
      $map['project_category_id'] = $projectCategoryId;
      $ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
      $ProjectCategoryRatioL->where($map)->delete();

      $this->success("操作成功",U('index?id=', I('get.')));
  }
}