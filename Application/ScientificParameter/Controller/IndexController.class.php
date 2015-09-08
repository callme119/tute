<?php
namespace ScientificParameter\Controller;
use Admin\Controller\AdminController;
use PublicProject\Model\PublicProjectModel;
use DatamodelOne\Model\DatamodelOneModel;
class IndexController extends AdminController {
  public function indexAction(){

    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
  }
  //管理员进行公共项目添加的界面
  public function addAction() {
    $projectM = new PublicProjectModel();
    $project = $projectM->init();
    $this->assign('project',$project);
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

  $projectM = new PublicProjectModel();
    $id = $projectM->saveProject();//返回存公共库成功的id
    if($id)
    {
       //判断使用的数据模型
     switch(I('post.type')){
      case 0:
      $this->success('操作成功','add');
      break;
      case 1:
      $dataModel = new DatamodelOneModel();
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

  $projectM = new PublicProjectModel();
  $res = $projectM->append($pid);
  $this->assign('data',$res);
  $return['data'] = $this->fetch();
    //echo $data;
  $this->ajaxReturn($return);
  
}
}
