<?php
namespace ScientificParameter\Controller;
use Admin\Controller\AdminController;
use ProjectCategory\Model\ProjectCategoryModel;
use DataModel\Model\DataModelModel;               //数据模型
use ProjectCategory\Logic\ProjectCategoryLogic;   //项目类别
use DataModelDetail\Model\DataModelDetailModel;
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
    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
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
  $ProjectCategoryM = new ProjectCategoryModel();
    $id = $ProjectCategoryM->saveProject();//返回存公共库成功的id
    if($id)
    {
       //判断使用的数据模型
     switch(I('post.type')){
      case 0:
      $this->success('操作成功','add');
      break;
      case 1:
      $dataModel = new DataModelOneModel();
      break;
      default:
      $this->error('操作失败','add');
      break;
    }

    if($dataModel->save($id))
    {
      $this->success('操作成功','add');
    }
    else{
      $this->error('操作失败','add');
    }
  }
  else{
    $this->error('操作失败','add');
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
}
