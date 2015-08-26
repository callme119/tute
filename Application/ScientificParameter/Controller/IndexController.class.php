<?php
namespace ScientificParameter\Controller;
use Admin\Controller\AdminController;
use PublicProject\Model\PublicProjectModel;
class IndexController extends AdminController {
  public function indexAction(){

    $this->assign('YZBODY',$this->fetch());
    $this->display(YZTemplate);
  }
  //管理员进行公共项目添加的界面
  public function addAction() {
    // $projectM = new \PublicProject\Model\PublicProjectModel();
    // $type = $projectM->getTypeById($id="3");
    // var_dump($type['type']);
    $append = U('append');
    $this->assign('url',$append);
    $projectM = new \PublicProject\Model\PublicProjectModel();
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
   $data = I('post.');
   $projectM = new \PublicProject\Model\PublicProjectModel();
   $state = $projectM->saveProject($data);
   if($state == "success"){
    $this->success('新增成功','add');
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

    $projectM = new \PublicProject\Model\PublicProjectModel();
    $res = $projectM->append($pid);
    $this->assign('data',$res);
    $return['data'] = $this->fetch();
    //echo $data;
    $this->ajaxReturn($return);
  
  }
}