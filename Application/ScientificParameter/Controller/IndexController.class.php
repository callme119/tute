<?php
namespace ScientificParameter\Controller;
use Admin\Controller\AdminController;
use ProjectCategory\Model\ProjectCategoryModel;
use DataModel\Model\DataModelModel;               //数据模型
use ProjectCategory\Logic\ProjectCategoryLogic;   //项目类别
use DataModelDetail\Model\DataModelDetailModel;
use ProjectCategoryRatio\Logic\ProjectCategoryRatioLogic; //项目类别系数表
use Project\Logic\ProjectLogic;                           //项目具体信息
class IndexController extends AdminController {
  public function indexAction(){
    //取类别数据,有深度，所以取的是树
    $ProjectCategoryL = new ProjectCategoryLogic();
    $projectCategoryTree = $ProjectCategoryL->getSonsTreeById(0);

    //树变数组
    $projectCategoryLists = tree_to_list($projectCategoryTree ,$i = 0, '_son');

    //取分页数据
    $ProjectCategoryL = new ProjectCategoryLogic();
    $lists = $ProjectCategoryL->getCurrentLists($projectCategoryLists);

    //传值
    $this->assign("totalCount",count($projectCategoryLists));
    $this->assign("lists",$lists);
    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
  }
  //管理员进行公共项目添加的界面
  public function addAction() {
    $ProjectCategoryL = new ProjectCategoryLogic();
    $id = 0;
   
    $projectCategoryTree = $ProjectCategoryL->getSonsTreeById($id);
    $projectCategory = tree_to_list($projectCategoryTree , $id , '_son' );
    
    //取数据模型列表
    $dataModelM = new DataModelModel();
    $dataModels = $dataModelM->getNormalLists();

    $this->assign("js",$this->fetch("addJs"));
    $this->assign("dataModels",$dataModels);
    $this->assign('projectCategory',$projectCategory);
    $this->assign('YZBODY',$this->fetch('add'));
    $this->display(YZTemplate);
  }

  public function editAction()
  {
    $this->addAction();
  }
  public function addProjectAction() {
    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
  }
  public function projectSetAction() {
   $this->assign('YZBODY',$this->fetch());
   $this->display(YZTemplate);
 }
 public function projectManageAction() {
   $this->assign('YZBODY',$this->fetch());
   $this->display(YZTemplate);
 }
  /***
  * 保存，添加或修改之后，提交到该方法
  * 将获取到的Post数据传递给M层
  */
  public function saveAction()
  {
      try
      {
          // dump(I('post.'));
          //添加项目类别信息
          $ProjectCategoryM = new ProjectCategoryModel();
          $projectCategoryId = $ProjectCategoryM->addListFromPost();
          // dump($projectCategoryId);

          //添加项目类别系数信息
          $ProjectCategoryRatioL = new ProjectCategoryRatioLogic();
          $dataModelDeatailIds = I('post.data_model_detail_id');
          $ProjectCategoryRatioL->addListsByProjectCategoryIdDataModelDetailIds($projectCategoryId,$dataModelDeatailIds);

          $this->success("操作成功",'add?pid='.I('post.pid'));

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
      if( !empty($ProjectCategoryL -> getListsByPid($projectCategoryId) )) 
      {
          $this->error("该项目结点存在子结点，请先删子结点。", U('index?p=' . I('get.p')));
          return;
      }

      //查看项目表中，是否有项目的类别已选该类别，已选则不能删。
      $ProjectL = new ProjectLogic();
      if(!empty($ProjectL -> getListsByProjectCategoryId($projectCategoryId)))
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

      echo $ProjectCategoryRatioL->getLastSql();
      $this->success("操作成功",U('index?p=' . I('get.p')));
  }
}
